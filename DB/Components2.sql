-- -----------------------------------------------------
-- Data for table `Component`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Component` (`CO_name`, `CO_address`, `CO_option`) VALUES 
('FSControl', 'localhost/uebungsplattform/FS/FSControl', ''),
('FSFile', 'localhost/uebungsplattform/FS/FSFile', ''),
('FSZip', 'localhost/uebungsplattform/FS/FSZip', ''),
('DBControl', 'localhost/uebungsplattform/DB/DBControl', ''),
('FSBinder', 'localhost/uebungsplattform/FS/FSBinder', ''),
('DBCourse', 'localhost/uebungsplattform/DB/DBCourse', ''),
('DBUser', 'localhost/uebungsplattform/DB/DBUser', ''),
('DBAttachment', 'localhost/uebungsplattform/DB/DBAttachment', ''),
('DBExercise', 'localhost/uebungsplattform/DB/DBExercise', ''),
('DBExerciseSheet', 'localhost/uebungsplattform/DB/DBExerciseSheet', ''),
('DBExerciseType', 'localhost/uebungsplattform/DB/DBExerciseType', ''),
('DBFile', 'localhost/uebungsplattform/DB/DBFile', ''),
('DBQuery', 'localhost/uebungsplattform/DB/DBQuery', ''),
('LController', 'localhost/uebungsplattform/logic/LController', ''),
('LGroup', 'localhost/uebungsplattform/logic/LGroup', ''),
('LUser', 'localhost/uebungsplattform/logic/LUser', ''),
('DBCourseStatus', 'localhost/uebungsplattform/DB/DBCourseStatus', ''),
('FSPdf', 'localhost/uebungsplattform/FS/FSPdf', ''),
('DBExternalId', 'localhost/uebungsplattform/DB/DBExternalId', ''),
('DBApprovalCondition', 'localhost/uebungsplattform/DB/DBApprovalCondition', ''),
('DBGroup', 'localhost/uebungsplattform/DB/DBGroup', ''),
('DBInvitation', 'localhost/uebungsplattform/DB/DBInvitation', ''),
('DBMarking', 'localhost/uebungsplattform/DB/DBMarking', ''),
('DBSession', 'localhost/uebungsplattform/DB/DBSession', ''),
('DBSubmission', 'localhost/uebungsplattform/DB/DBSubmission', ''),
('DBSelectedSubmission', 'localhost/uebungsplattform/DB/DBSelectedSubmission', ''),
('LTutor', 'localhost/uebungsplattform/logic/LTutor', ''),
('LExerciseSheet', 'localhost/uebungsplattform/logic/LExerciseSheet', ''),
('LExercise', 'localhost/uebungsplattform/logic/LExercise', ''),
('LCondition', 'localhost/uebungsplattform/logic/LCondition', ''),
('LAttachment', 'localhost/uebungsplattform/logic/LAttachment', ''),
('LExerciseType', 'localhost/uebungsplattform/logic/LExerciseType', ''),
('LGetSite', 'localhost/uebungsplattform/logic/LGetSite', ''),
('DBExerciseFileType', 'localhost/uebungsplattform/DB/DBExerciseFileType', ''),
('CControl', 'localhost/uebungsplattform/DB/CControl', ''),
('LCourse', 'localhost/uebungsplattform/logic/LCourse', ''),
('LSubmission', 'localhost/uebungsplattform/logic/LSubmission', ''),
('LMarking', 'localhost/uebungsplattform/logic/LMarking', ''),
('DBQuery2', 'localhost/uebungsplattform/DB/DBQuery2', ''),
('LFile', 'localhost/uebungsplattform/logic/LFile', ''),
('DBForm', 'localhost/uebungsplattform/DB/DBForm', ''),
('DBChoice', 'localhost/uebungsplattform/DB/DBChoice', ''),
('LFormPredecessor', 'localhost/uebungsplattform/logic/LFormPredecessor', ''),
('LFormProcessor', 'localhost/uebungsplattform/logic/LFormProcessor', ''),
('LForm', 'localhost/uebungsplattform/logic/LForm', ''),
('LProcessor', 'localhost/uebungsplattform/logic/LProcessor', ''),
('DBProcess', 'localhost/uebungsplattform/DB/DBProcess', ''),
('DBProcessAttachment', 'localhost/uebungsplattform/DB/DBAttachment2/processAttachment', ''),
('DBProcessWorkFiles', 'localhost/uebungsplattform/DB/DBAttachment2/processWorkFiles', ''),
('LExtension', 'localhost/uebungsplattform/logic/LExtension', ''),
('LOOP', 'localhost/uebungsplattform/logic/LOOP', ''),
('DBProcessList', 'localhost/uebungsplattform/DB/DBProcess/processList', ''),
('DBFormResult', 'localhost/uebungsplattform/DB/DBChoice/formResult', ''),
('CFilter', 'localhost/uebungsplattform/DB/CFilter', ''),
('DBTransaction', 'localhost/uebungsplattform/DB/DBTransaction', '')
ON DUPLICATE KEY UPDATE CO_address=VALUES(CO_address), CO_option=VALUES(CO_option);
COMMIT;


-- -----------------------------------------------------
-- Data for table `ComponentLinkage`
-- -----------------------------------------------------
START TRANSACTION;
TRUNCATE TABLE `ComponentLinkage`;
SET @DBForm = (select CO_id from Component where CO_name='DBForm' limit 1);
SET @DBChoice = (select CO_id from Component where CO_name='DBChoice' limit 1);
SET @LFormPredecessor = (select CO_id from Component where CO_name='LFormPredecessor' limit 1);
SET @LFormProcessor = (select CO_id from Component where CO_name='LFormProcessor' limit 1);
SET @LForm = (select CO_id from Component where CO_name='LForm' limit 1);
SET @LOOP = (select CO_id from Component where CO_name='LOOP' limit 1);
SET @DBFormResult = (select CO_id from Component where CO_name='DBFormResult' limit 1);
SET @DBQuery2 = (select CO_id from Component where CO_name='DBQuery2' limit 1);
SET @DBProcessList = (select CO_id from Component where CO_name='DBProcessList' limit 1);
SET @FSPdf = (select CO_id from Component where CO_name='FSPdf' limit 1);
SET @LController = (select CO_id from Component where CO_name='LController' limit 1);
SET @DBControl = (select CO_id from Component where CO_name='DBControl' limit 1);
SET @LExtension = (select CO_id from Component where CO_name='LExtension' limit 1);
SET @FSControl = (select CO_id from Component where CO_name='FSControl' limit 1);
SET @FSFile = (select CO_id from Component where CO_name='FSFile' limit 1);
SET @FSZip = (select CO_id from Component where CO_name='FSZip' limit 1);
SET @FSBinder = (select CO_id from Component where CO_name='FSBinder' limit 1);
SET @DBCourse = (select CO_id from Component where CO_name='DBCourse' limit 1);
SET @DBUser = (select CO_id from Component where CO_name='DBUser' limit 1);
SET @DBAttachment = (select CO_id from Component where CO_name='DBAttachment' limit 1);
SET @DBExercise = (select CO_id from Component where CO_name='DBExercise' limit 1);
SET @DBExerciseSheet = (select CO_id from Component where CO_name='DBExerciseSheet' limit 1);
SET @DBExerciseType = (select CO_id from Component where CO_name='DBExerciseType' limit 1);
SET @DBFile = (select CO_id from Component where CO_name='DBFile' limit 1);
SET @DBQuery = (select CO_id from Component where CO_name='DBQuery' limit 1);
SET @LGroup = (select CO_id from Component where CO_name='LGroup' limit 1);
SET @LUser = (select CO_id from Component where CO_name='LUser' limit 1);
SET @DBCourseStatus = (select CO_id from Component where CO_name='DBCourseStatus' limit 1);
SET @DBExternalId = (select CO_id from Component where CO_name='DBExternalId' limit 1);
SET @DBApprovalCondition = (select CO_id from Component where CO_name='DBApprovalCondition' limit 1);
SET @DBGroup = (select CO_id from Component where CO_name='DBGroup' limit 1);
SET @DBInvitation = (select CO_id from Component where CO_name='DBInvitation' limit 1);
SET @DBMarking = (select CO_id from Component where CO_name='DBMarking' limit 1);
SET @DBSession = (select CO_id from Component where CO_name='DBSession' limit 1);
SET @DBSubmission = (select CO_id from Component where CO_name='DBSubmission' limit 1);
SET @DBSelectedSubmission = (select CO_id from Component where CO_name='DBSelectedSubmission' limit 1);
SET @LTutor = (select CO_id from Component where CO_name='LTutor' limit 1);
SET @LExerciseSheet = (select CO_id from Component where CO_name='LExerciseSheet' limit 1);
SET @LExercise = (select CO_id from Component where CO_name='LExercise' limit 1);
SET @LCondition = (select CO_id from Component where CO_name='LCondition' limit 1);
SET @LAttachment = (select CO_id from Component where CO_name='LAttachment' limit 1);
SET @LExerciseType = (select CO_id from Component where CO_name='LExerciseType' limit 1);
SET @LGetSite = (select CO_id from Component where CO_name='LGetSite' limit 1);
SET @DBExerciseFileType = (select CO_id from Component where CO_name='DBExerciseFileType' limit 1);
SET @CControl = (select CO_id from Component where CO_name='CControl' limit 1);
SET @LCourse = (select CO_id from Component where CO_name='LCourse' limit 1);
SET @LSubmission = (select CO_id from Component where CO_name='LSubmission' limit 1);
SET @LMarking = (select CO_id from Component where CO_name='LMarking' limit 1);
SET @LFile = (select CO_id from Component where CO_name='LFile' limit 1);
SET @LProcessor = (select CO_id from Component where CO_name='LProcessor' limit 1);
SET @DBProcess = (select CO_id from Component where CO_name='DBProcess' limit 1);
SET @DBProcessAttachment = (select CO_id from Component where CO_name='DBProcessAttachment' limit 1);
SET @DBProcessWorkFiles = (select CO_id from Component where CO_name='DBProcessWorkFiles' limit 1);
SET @CFilter = (select CO_id from Component where CO_name='CFilter' limit 1);
SET @DBTransaction = (select CO_id from Component where CO_name='DBTransaction' limit 1);

INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@FSControl, 'out', '', @FSFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@FSControl, 'out', '', @FSZip);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBCourse, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBUser, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBAttachment, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExercise, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExerciseSheet, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExerciseType, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBFile, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBCourse);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBUser);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBAttachment);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBExercise);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBExerciseSheet);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBExerciseType);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LCourse);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LGroup);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LUser);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'database', '', @DBControl);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'filesystem', '', @FSControl);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@FSControl, 'out', '', @FSPdf);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBSession);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBExternalId);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBSession, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExternalId, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBApprovalCondition);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBGroup);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBInvitation);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBApprovalCondition, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBGroup, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBInvitation, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBMarking);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBMarking, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBCourseStatus);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBCourseStatus, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBSelectedSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBSelectedSubmission, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBSubmission, 'out', '', @DBQuery);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LExerciseSheet);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LMarking);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LTutor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LExercise);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LCondition);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LAttachment);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LAttachment, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCondition, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LUser, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExercise, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExerciseSheet, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGroup, 'controller', '', @LController);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LMarking, 'controller', '', @LController);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LSubmission, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'controller', '', @LController);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LGetSite);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBExerciseFileType);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExerciseFileType, 'out', '', @DBQuery);

INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFile, 'file', '', @FSFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFile, 'fileDb', '', @DBFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LSubmission, 'file', '', @LFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LSubmission, 'submission', '', @DBSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LSubmission, 'selectedSubmission', '', @DBSelectedSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LMarking, 'file', '', @LFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LMarking, 'marking', '', @DBMarking);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBForm, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBChoice, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'marking', '', @LMarking);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'submission', '', @LSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'processorDb', '', @DBProcess);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBProcess, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormPredecessor, 'formDb', '', @DBForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormProcessor, 'formDb', '', @DBForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormPredecessor, 'pdf', '', @FSPdf);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormProcessor, 'pdf', '', @FSPdf);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LOOP, 'pdf', '', @FSPdf);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'postCourse', '', @DBCourse);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'deleteCourse', '', @DBCourse);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExtension, 'extension', '', @LForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LSubmission, 'zip', '', @FSZip);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LForm, 'choice', '', @DBChoice);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LForm, 'form', '', @DBForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @CControl);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LController, 'out', '', @LProcessor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBProcessAttachment, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBProcessWorkFiles, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'attachment', '', @DBProcessAttachment);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'workFiles', '', @DBProcessWorkFiles);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'file', '', @LFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBControl, 'out', '', @DBProcess);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LForm, 'postCourse', '', @DBForm);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LForm, 'postCourse', '', @DBChoice);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'postCourse', '', @DBProcess);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'postCourse', '', @DBProcessAttachment);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'postCourse', '', @DBProcessWorkFiles);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LProcessor, 'postCourse', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExtension, 'extension', '', @LFormPredecessor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExtension, 'extension', '', @LFormProcessor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExtension, 'extension', '', @LOOP);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExtension, 'extension', '', @LProcessor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBProcessList, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormPredecessor, 'postProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormProcessor, 'postProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LOOP, 'postProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormPredecessor, 'deleteProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormProcessor, 'deleteProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LOOP, 'deleteProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormPredecessor, 'getProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LFormProcessor, 'getProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LOOP, 'getProcess', '', @DBProcessList);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'postCourse', '', @LProcessor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'deleteCourse', '', @LProcessor);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'deleteCourse', '', @LExtension);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBFormResult, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LForm, 'postCourse', '', @DBFormResult);

INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBApprovalCondition, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBAttachment, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBCourse, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBCourseStatus, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExercise, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExerciseFileType, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExerciseSheet, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExerciseType, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBExternalId, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBFile, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBGroup, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBInvitation, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBMarking, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBSubmission, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBSelectedSubmission, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBSession, 'out2', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBUser, 'out2', '', @DBQuery2);

INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExerciseSheet, 'postFile', '', @LFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExerciseSheet, 'deleteFile', '', @LFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExerciseSheet, 'postExerciseSheet', '', @DBExerciseSheet);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExerciseSheet, 'getExerciseSheet', '', @DBExerciseSheet);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExerciseSheet, 'deleteExerciseSheet', '', @DBExerciseSheet);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@CFilter, 'out', 'GET_/file/:a/:b GET_/pdf/:a/:b GET_/zip/:a/:b', @FSControl);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LAttachment, 'postFile', '', @LFile);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LExercise, 'postAttachment', '', @LAttachment);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LAttachment, 'postAttachment', '', @DBAttachment);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@DBTransaction, 'out', '', @DBQuery2);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'postCourse', '', @DBTransaction);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LCourse, 'deleteCourse', '', @DBTransaction);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'postTransaction', '', @DBTransaction);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'getTransaction', '', @DBTransaction);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'postZip', '', @FSZip);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'postMarking', '', @LMarking);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'getMarking', '', @DBMarking);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LTutor, 'getExercise', '', @DBExercise);

INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getExerciseType', '', @DBExerciseType);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getExercise', '', @DBExercise);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getApprovalCondition', '', @DBApprovalCondition);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getUser', '', @DBUser);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getMarking', '', @DBMarking);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getSelectedSubmission', '', @DBSelectedSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getGroup', '', @DBGroup);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getCourseStatus', '', @DBCourseStatus);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getSubmission', '', @DBSubmission);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getCourse', '', @DBCourse);
INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (@LGetSite, 'getInvitation', '', @DBInvitation);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (00, '', '', 00);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (00, '', '', 00);
#INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (00, '', '', 00);
COMMIT;