#!/bin/bash
## @file setup.sh
## A script that helps to set up a new instance of the software.
## @author Florian Lücke
##
## @todo Copy files to the webdirectory

## Displays a usage message
function usage () {
    scriptname=$(basename $0)
    echo -ne "Usage:\t"
    echo $scriptname "[-hit] [-d databaseName] [-p password] [-s sqlserver] [-u username] [-f folderName]"
}

## Displays a help message.
function help () {
    usage
    echo
    echo -e "    -d name"
    echo -e "\tUse 'name' as database name."
    echo
    echo -e "    -f folderName"
    echo -e "\tThe name of the folder the software is installed in."
    echo
    echo -e "    -h"
    echo -e "\tDisplay this help message."
    echo
    echo -e "    -i"
    echo -e "\tRead required data interactively. Overrides -d -p -u -w."
    echo
    echo -e "    -p password"
    echo -e "\tUse 'password' as password for the database."
    echo
    echo -e "    -u username"
    echo -e "\tUse 'username' as username for the database."
    echo
    echo -e "    -s sqlserver"
    echo -e "\tUse 'sqlserver' as URL for the SQL Server. Defaults to localhost."
    echo
    echo -e "    -t"
    echo -e "\tInsert test data into the database."
}

## Checks if required programs exist.
function checkRequirements () {
    echo "Checking Reuirements..."

    echo -n "Testing for apache... "
    if hash apachectl &> /dev/null; then
        # apache is installed
        echo "found"

        # search required modules in all loaded modules
        hasRewrite=$(apachectl -t -D DUMP_MODULES 2>/dev/null | grep rewrite)
        hasPHP=$(apachectl -t -D DUMP_MODULES 2>/dev/null | grep php)

        # check if mod_rewrite is loaded
        echo -n "  Testing for mod_rewrite... "
        if [[ "$hasRewrite" = "" ]]; then
            # mod_rewrite is not loaded
            echo "not found"
            echo "Please enable mod_rewrite for apache to continue." >&2
            exit 1
        else
            echo "found"
        fi

        # check if mod_php is loaded
        echo -n "  Testing for mod_php... "
        if [[ "$hasPHP" = "" ]]; then
            # mod_rewrite is not loaded
            echo "not found"
            echo "Please enable mod_php for apache to continue." >&2
            exit 1
        else
            echo "found"
        fi
    else
        echo "not found"
        echo "Please install apache to continue." >&2
        exit 1
    fi

    # check if mysql is installed
    hash mysql &> /dev/null || { echo "Could not find mysql, quitting!" >&2; exit 1; }
}

## Checks if user-provided credentials are valid.
##
## @var $1 The URL to the MySQL Server we are trying to connect to
## @var $2 The username of the user that should have access to the database
## @var $3 The user's password.
##
## @todo check grants for the user
## @todo actually check if the connection was successful
function checkMySQLCredentials() {
    local server=$1
    local uname=$2
    local pass=$3

    echo -n "Connecting to MySQL Server: $server... "
    access=$(mysqladmin -u$uname -p$pass -h$server ping 2>&1 | grep -o "Access denied")

    success=$?

    if [[ $access != "" ]]; then
        echo "Invalid credentials for server $server." >&2
        exit 1
    else
        echo "success"
    fi
}

## Reads user data interactively.
function readUserData () {
    read -a username_a -p "Enter database username: "
    username="${username_a[*]}"
    read -sa password_a -p "Enter database password: "
    password="${password_a[*]}"
    echo
    read -a sqlserver_a -p "Enter database URL (default: localhost): "
    server_no_sp="${sqlserver_a[*]}"
    sqlserver=${server_no_sp:-"localhost"}
    read -a webdir_a -p "Enter name of the folder the software is installed in (default: uebungsplattform): "
    webdir="${webdir_a[*]}"
    read -a databasename_a -p "Enter database name (default: uebungsplattform): "
    databaseName="${databasename_a[*]}"
    read -p "Insert test data? [Y/N] " inserData
    inserData=$(echo $inserData | tr [:lower:] [:upper:])

    if [[ $inserData = "Y" ]]; then
        testData=1
    fi
}

username=""
password=""
webdir=""
databaseName=""
testData=0

if [[ $# -eq 0 ]]; then
    # no command line arguments, nothing to do
    usage $@
    exit 1
fi

# All requirements are met. Evaluate command line options.
while getopts 'd:f:ihp:s:u:t' optname; do
    case "$optname" in
    "d")
        databaseName=$OPTARG;;
    "h")
        help; exit 1;;
    "i")
        readUserData; break;;
    "p")
        password=$OPTARG;;
    "u")
        username=$OPTARG;;
    "s")
        sqlserver=$OPTARG;;
    "f")
        webdir=$OPTARG;;
    "t")
        testData=1;;
    ?)
        usage; exit 1;;
    *)
        usage; exit 1;;
    esac
done

# set sqlserver to default value, if unset
sqlserver=${sqlserver:-"localhost"}
databaseName=${databaseName:-"uebungsplattform"}
webdir=${webdir:-"uebungsplattform"}

# test if all neccessary data is defined
if [[ -z $username ]]; then
    read -p "No username specified. Do you wish to continue? [Y/N] " shouldContinue
    shouldContinue=$(echo $shouldContinue | tr [:lower:] [:upper:])

    if [[ $shouldContinue = "N" ]]; then
        exit 1
    fi
fi

if [[ -z $password ]]; then
    read -p "No password specified. Do you wish to continue? [Y/N] " shouldContinue
    shouldContinue=$(echo $shouldContinue | tr [:lower:] [:upper:])

    if [[ $shouldContinue = "N" ]]; then
        exit 1
    fi
fi

## @todo update the tests so they are actually useful

# # Check requirements.
# checkRequirements

# # Check if we can access the SQL Server.
# checkMySQLCredentials ${sqlserver} ${username} ${password}

dirname=$(dirname $0)
cd $dirname
dirname=$(pwd)

# place the supplied username, password and database URL in the configuration
# files
echo -n "Configuring SQL settings... "
find . -name 'config.ini' -exec sed -i.bak\
    -e "s/^db_user.*$/db_user = $username/g"\
    -e "s/^db_passwd.*$/db_passwd = $password/g"\
    -e "s/^db_path.*$/db_path = $sqlserver/g"\
    -e "s/uebungsplattform/$databaseName/g" {} \;
echo "done"

# set the database name according to user preferences
if [[ $databaseName != 'uebungsplattform' ]]; then
    echo "Configuring database name... "
    find . -name 'Database.sql' -or -name 'Components.sql'\
        -or -name 'Sample.sql' -exec sed -i.bak\
        -e "s/\`uebungsplattform\`/\`$databaseName\`/g" {} \;
    echo "done"
fi

find . -name '*.bak' -delete

# update component
if [[ $directoryPrefix != "uebungsplattform" ]]; then
    echo -n  "Configuring webdirectory... "
    find . -name 'Components.sql' -or -name 'Sample.sql' -exec sed -i.bak\
        -e "s#localhost/uebungsplattform#localhost/$webdir#g" {} \;
    echo "done"
fi

find . -name '*.bak' -delete

# create CConfig files to store the component configurations
echo "Creating CConfig files... "
find . -name 'CConfig.json' -delete
echo -n "   for database... "
find DB \( -name 'DB*' -or -name 'CC*' \) -type d -d 1 -exec touch {}/CConfig.json \;
echo "done"
echo -n "   for filesystem... "
find FS -name 'FS*' -type d -d 1 -exec touch {}/CConfig.json \;
echo "done"
echo -n "   for logic... "
find logic \! \( -name 'Include' \) -type d -d 1 -exec touch {}/CConfig.json \;
echo "done"

# make CConfig.json readable and writable for everyone
echo -n "Making files writable... "
find . -name 'CConfig.json' -exec chmod 777 {} \;
echo "done"


# set up database
echo "Setting up database..."
echo -n "    Creating schema... "
find . -name 'Database.sql' -print0 | xargs -0 cat | mysql -u$username -p$password -h$sqlserver 2&>/dev/null
echo "done"

if [[ $testData -eq 1 ]]; then
    echo -n "    Inserting test data ... "
    find . -name 'Sample.sql' -print0 | xargs -0 cat | mysql -u$username -p$password -h$sqlserver -f 2&>/dev/null
    echo "done"
else
    echo -n "    Setting up components ... "
    find . -name 'Components.sql' -print0 | xargs -0 cat | mysql -u$username -p$password -h$sqlserver 2&>/dev/null
    echo "done"
fi

echo "Sending component configuration to all components"
curl -X GET "localhost/$webdir/DB/CControl/send"

echo "Configuring UI"
find . -name 'Config.php' -exec sed -i.bak\
    -e "s#http://141.48.9.92/uebungsplattform#http://localhost/$webdir#g" {} \;

echo "All done."

find . -name '*.bak' -delete

echo "We created a user 'super-admin' with password 'test' for you. We highly recommend changing the password!"