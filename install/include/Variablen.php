<?php
class Variablen
{
    public static $DBVariablen = array( 'db_user_insert',
                                        'db_first_name_insert',
                                        'db_last_name_insert',
                                        'db_email_insert',
                                        'db_passwd_insert',
                                        'componentsSql',
                                        'db_ignore',
                                        'db_override',
                                        'db_path',
                                        'db_name',
                                        'db_user',
                                        'db_passwd',
                                        'db_user_operator',
                                        'db_passwd_operator',
                                        'db_user_override_operator');
                                        
    public static $UIVariablen = array( 'conf');
    
    public static $SVVariablen = array( 'name');
    public static $PLUGVariablen = array( 'plug_install_CORE',
                                          'plug_install_OSTEPU-UI',
                                          'plug_install_OSTEPU-DB',
                                          'plug_install_OSTEPU-FS',
                                          'plug_install_OSTEPU-LOGIC',
                                          'plug_install_INSTALL');
                                        
    public static $PLVariablen = array( 'pl_main_details',
                                        'url',
                                        'urlExtern',
                                        'temp',
                                        'files',
                                        'pl_details');
                                        
    public static $ZVVariablen = array( 'zv_type',
                                        'zv_ssh_login',
                                        'zv_ssh_password',
                                        'zv_ssh_key_file',
                                        'zv_ssh_auth_type',
                                        'zv_ssh_address');
                                        
    public static $COVariablen = array( 'co_details',
                                        'co_link_type',
                                        'co_link_availability');  
    
    public static function Initialisieren(&$data)
    {
        foreach (Variablen::$DBVariablen as $var)
            $data['DB'][$var] = isset($data['DB'][$var]) ? $data['DB'][$var] : null;
        foreach (Variablen::$UIVariablen as $var)
            $data['UI'][$var] = isset($data['UI'][$var]) ? $data['UI'][$var] : null;
        foreach (Variablen::$PLVariablen as $var)
            $data['PL'][$var] = isset($data['PL'][$var]) ? $data['PL'][$var] : null;
        foreach (Variablen::$COVariablen as $var)
            $data['CO'][$var] = isset($data['CO'][$var]) ? $data['CO'][$var] : null;
        foreach (Variablen::$SVVariablen as $var)
            $data['SV'][$var] = isset($data['SV'][$var]) ? $data['SV'][$var] : null;
        foreach (Variablen::$ZVVariablen as $var)
            $data['ZV'][$var] = isset($data['ZV'][$var]) ? $data['ZV'][$var] : null;
        foreach (Variablen::$PLUGVariablen as $var)
            $data['PLUG'][$var] = isset($data['PLUG'][$var]) ? $data['PLUG'][$var] : null;
    }
    
    public static function Einsetzen(&$data)
    {
        foreach (Variablen::$DBVariablen as $var)
            Einstellungen::GetValue("data[DB][{$var}]",$data['DB'][$var]);
        foreach (Variablen::$UIVariablen as $var)
            Einstellungen::GetValue("data[UI][{$var}]",$data['UI'][$var]);
        foreach (Variablen::$PLVariablen as $var)
            Einstellungen::GetValue("data[PL][{$var}]",$data['PL'][$var]);
        foreach (Variablen::$COVariablen as $var)
            Einstellungen::GetValue("data[CO][{$var}]",$data['CO'][$var]);
        foreach (Variablen::$SVVariablen as $var)
            Einstellungen::GetValue("data[SV][{$var}]",$data['SV'][$var]);
        foreach (Variablen::$ZVVariablen as $var)
            Einstellungen::GetValue("data[ZV][{$var}]",$data['ZV'][$var]);
        foreach (Variablen::$PLUGVariablen as $var)
            Einstellungen::GetValue("data[PLUG][{$var}]",$data['PLUG'][$var]);
    }
    
    public static function EinsetzenDirekt($konfiguration,&$data)
    {
        foreach (Variablen::$DBVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[DB][{$var}]",$data['DB'][$var]);
        foreach (Variablen::$UIVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[UI][{$var}]",$data['UI'][$var]);
        foreach (Variablen::$PLVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[PL][{$var}]",$data['PL'][$var]);
        foreach (Variablen::$COVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[CO][{$var}]",$data['CO'][$var]);
        foreach (Variablen::$SVVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[SV][{$var}]",$data['SV'][$var]);
        foreach (Variablen::$ZVVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[ZV][{$var}]",$data['ZV'][$var]);
        foreach (Variablen::$PLUGVariablen as $var)
            Einstellungen::GetValueDirekt($konfiguration,"data[PLUG][{$var}]",$data['PLUG'][$var]);
    }
    
    public static function Zuruecksetzen(&$data)
    {
        foreach (Variablen::$DBVariablen as $var)
            $data['DB'][$var] = null;
        foreach (Variablen::$UIVariablen as $var)
            $data['UI'][$var] = null;
        foreach (Variablen::$PLVariablen as $var)
            $data['PL'][$var] = null;
        foreach (Variablen::$COVariablen as $var)
            $data['CO'][$var] = null;
        foreach (Variablen::$SVVariablen as $var)
            $data['SV'][$var] = null;
        foreach (Variablen::$ZVVariablen as $var)
            $data['ZV'][$var] = null;
        foreach (Variablen::$PLUGVariablen as $var)
            $data['PLUG'][$var] = null;
    }
}