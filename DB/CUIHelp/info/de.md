<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

Die CUIHelp enthält einige Hilfedateien der Benutzerschicht (`UI`)

| Themen |
| :- |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit CUIHelp als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|request|
| :----------- |:----- |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|
|| GET /help/:language/page/admin/tutorUpload/upload.md|
|| GET /help/:language/page/admin/tutorUpload/uploadA.png|
|| GET /help/:language/page/admin/tutorUpload/uploadB.png|
|| GET /help/:language/page/admin/tutorUpload/uploadC.png|
|| GET /help/:language/page/admin/tutorUpload/uploadD.png|
|| GET /help/:language/page/admin/tutorUpload/sampleD.png|
|| GET /help/:language/setting/AllowLateSubmissions/AllowLateSubmissionsDesc.md|
|| GET /help/:language/setting/AllowLateSubmissions/allowLateSubmissionsA.png|
|| GET /help/:language/setting/AllowLateSubmissions/allowLateSubmissionsB.png|
|| GET /help/:language/setting/AllowLateSubmissions/allowLateSubmissionsC.png|
|| GET /help/:language/setting/AllowLateSubmissions/allowLateSubmissionsD.png|
|| GET /help/:language/setting/RegistrationPeriodEnd/RegistrationPeriodEndDesc.md|
|| GET /help/:language/setting/RegistrationPeriodEnd/periodExpiredA.png|
|| GET /help/:language/setting/InsertStudentNamesIntoTutorArchives/InsertStudentNamesIntoTutorArchivesDesc.md|
|| GET /help/:language/setting/InsertStudentNamesIntoTutorArchives/insertStudentNamesIntoTutorArchivesA.png|
|| GET /help/:language/page/student/groups/A.png|
|| GET /help/:language/page/student/groups/B.png|
|| GET /help/:language/page/student/groups/C.png|
|| GET /help/:language/page/student/groups/Cf.png|
|| GET /help/:language/page/student/groups/D.png|
|| GET /help/:language/page/student/groups/E.png|
|| GET /help/:language/page/student/groups/F.png|
|| GET /help/:language/page/student/groups/G.png|
|| GET /help/:language/page/student/groups/H.png|
|| GET /help/:language/page/student/groups/I.png|
|| GET /help/:language/page/student/groups/invitationsToGroup.md|
|| GET /help/:language/page/student/groups/invitationsFromGroup.md|
|| GET /help/:language/page/student/groups/groupMembers.md|
|| GET /help/:language/page/student/groups/groupManagement.md|
|| GET /help/:language/page/admin/markingTool/createArchive.md|
|| GET /help/:language/page/admin/markingTool/sampleB.png|
|| GET /help/:language/page/admin/markingTool/libreA.png|
|| GET /help/:language/page/admin/markingTool/libreB.png|
|| GET /help/:language/page/admin/markingTool/pathA.png|
|| GET /help/:language/page/admin/markingTool/pathB.png|
|| GET /help/:language/page/admin/markingTool/pathC.png|
|| GET /help/:language/page/admin/markingTool/pathD.png|
|| GET /help/:language/page/admin/markingTool/pathE.png|
|| GET /help/:language/page/admin/markingTool/filter.md|
|| GET /help/:language/page/admin/markingTool/filterA.png|
|| GET /help/:language/page/admin/markingTool/filterB.png|
|| GET /help/:language/page/admin/markingTool/filterC.png|
|| GET /help/:language/page/admin/markingTool/filterD.png|
|| GET /help/:language/page/admin/markingTool/filterE.png|
|| GET /help/:language/page/admin/markingTool/work.md|
|| GET /help/:language/page/admin/markingTool/workA.png|
|| GET /help/:language/page/admin/markingTool/workB.png|
|| GET /help/:language/page/admin/markingTool/workC.png|
|| GET /help/:language/page/admin/markingTool/workD.png|
|| GET /help/:language/page/admin/markingTool/workE.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsA.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsB.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsC.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsD.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsE.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsF.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsG.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsH.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsI.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsJ.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsK.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsL.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsM.png|
|| GET /help/:language/page/admin/createSheet/sheetSettingsN.png|
|| GET /help/:language/page/admin/createSheet/sheetSettings.md|
|| GET /help/:language/page/admin/createSheet/createExerciseA.png|
|| GET /help/:language/page/admin/createSheet/createExerciseB.png|
|| GET /help/:language/page/admin/createSheet/createExerciseC.png|
|| GET /help/:language/page/admin/createSheet/createExerciseD.png|
|| GET /help/:language/page/admin/createSheet/createExerciseE.png|
|| GET /help/:language/page/admin/createSheet/createExerciseF.png|
|| GET /help/:language/page/admin/createSheet/createExerciseG.png|
|| GET /help/:language/page/admin/createSheet/createExerciseH.png|
|| GET /help/:language/page/admin/createSheet/createExerciseI.png|
|| GET /help/:language/page/admin/createSheet/exercise.md|
|| GET /help/:language/page/admin/tutorAssign/automatically.md|
|| GET /help/:language/page/admin/tutorAssign/autoA.png|
|| GET /help/:language/page/admin/tutorAssign/autoB.png|
|| GET /help/:language/page/admin/tutorAssign/autoC.png|
|| GET /help/:language/page/admin/tutorAssign/make.md|
|| GET /help/:language/page/admin/tutorAssign/makeA.png|
|| GET /help/:language/page/admin/tutorAssign/makeB.png|
|| GET /help/:language/page/admin/tutorAssign/makeC.png|
|| GET /help/:language/page/admin/tutorAssign/makeD.png|
|| GET /help/:language/page/admin/tutorAssign/makeE.png|
|| GET /help/:language/page/admin/tutorAssign/manually.md|
|| GET /help/:language/page/admin/tutorAssign/manA.png|
|| GET /help/:language/page/admin/tutorAssign/manB.png|
|| GET /help/:language/page/admin/tutorAssign/manC.png|
|| GET /help/:language/page/admin/tutorAssign/manD.png|
|| GET /help/:language/page/admin/tutorAssign/manE.png|
|| GET /help/:language/page/admin/tutorAssign/manF.png|
|| GET /help/:language/page/admin/tutorAssign/manG.png|
|| GET /help/:language/page/admin/tutorAssign/remove.md|
|| GET /help/:language/page/admin/tutorAssign/removeA.png|
|| GET /help/:language/page/admin/tutorAssign/removeB.png|
|| GET /help/:language/page/admin/tutorAssign/removeC.png|
|| GET /help/:language/extension/LFormPredecessor/LFormPredecessorDesc.md|
|| GET /help/:language/extension/LFormPredecessor/LFormPredecessor.png|
|| GET /help/:language/extension/LFormProcessor/LFormProcessorDesc.md|
|| GET /help/:language/extension/LFormProcessor/LFormProcessor.png|
|| GET /help/:language/extension/LForm/LFormDesc.md|
|| GET /help/:language/extension/LForm/formCheckbox.md|
|| GET /help/:language/extension/LForm/MehrfachauswahlVerwenden3.png|
|| GET /help/:language/extension/LForm/MehrfachauswahlVerwendenSample.png|
|| GET /help/:language/extension/LForm/formInput.md|
|| GET /help/:language/extension/LForm/EingabezeileVerwenden3.png|
|| GET /help/:language/extension/LForm/EingabezeileVerwendenSample.png|
|| GET /help/:language/extension/LForm/formRadio.md|
|| GET /help/:language/extension/LForm/EinfachauswahlVerwenden3.png|
|| GET /help/:language/extension/LForm/EinfachauswahlVerwendenSample.png|
|| GET /help/:language/extension/LProcessor/LProcessorDesc.md|
|| GET /help/:language/page/admin/courseManagement/courseSettings.md|
|| GET /help/:language/page/admin/courseManagement/courseSettingsA.png|
|| GET /help/:language/page/admin/courseManagement/courseSettingsB.png|
|| GET /help/:language/page/admin/courseManagement/courseSettingsC.png|
|| GET /help/:language/page/admin/courseManagement/courseSettingsD.png|
|| GET /help/:language/page/admin/courseManagement/courseSettingsE.png|
|| GET /help/:language/page/admin/courseManagement/courseSettingsF.png|
|| GET /help/:language/page/admin/courseManagement/courseSettingsG.png|
|| GET /help/:language/page/admin/courseManagement/courseNotifications.md|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsA.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsB.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsC.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsD.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsE.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsF.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsG.png|
|| GET /help/:language/page/admin/courseManagement/courseNotificationsH.png|
|| GET /help/:language/page/admin/courseManagement/courseRedirects.md|
|| GET /help/:language/page/admin/courseManagement/courseRedirectsA.png|
|| GET /help/:language/page/admin/courseManagement/courseRedirectsB.png|
|| GET /help/:language/page/admin/courseManagement/courseRedirectsC.png|
|| GET /help/:language/page/admin/courseManagement/courseRedirectsD.png|
|| GET /help/:language/page/admin/courseManagement/courseRedirectsE.png|
|| GET /help/:language/page/admin/courseManagement/courseRedirectsF.png|
|| GET /help/:language/page/admin/courseManagement/addExternalId.md|
|| GET /help/:language/page/admin/courseManagement/addExternalIdA.png|
|| GET /help/:language/page/admin/courseManagement/addExternalIdB.png|
|| GET /help/:language/page/admin/courseManagement/addExternalIdC.png|
|| GET /help/:language/page/admin/courseManagement/addExternalIdD.png|
|| GET /help/:language/page/admin/courseManagement/addExternalIdE.png|
|| GET /help/:language/page/admin/courseManagement/editExternalId.md|
|| GET /help/:language/page/admin/courseManagement/editExternalIdA.png|
|| GET /help/:language/page/admin/courseManagement/editExternalIdB.png|
|| GET /help/:language/page/admin/courseManagement/editExternalIdC.png|
|| GET /help/:language/page/admin/courseManagement/revokeRights.md|
|| GET /help/:language/page/admin/courseManagement/revokeRightsA.png|
|| GET /help/:language/page/admin/courseManagement/revokeRightsB.png|
|| GET /help/:language/page/admin/courseManagement/revokeRightsC.png|
|| GET /help/:language/page/admin/courseManagement/addUser.md|
|| GET /help/:language/page/admin/courseManagement/addUserA.png|
|| GET /help/:language/page/admin/courseManagement/addUserB.png|
|| GET /help/:language/page/admin/courseManagement/addUserC.png|
|| GET /help/:language/page/admin/courseManagement/addUserD.png|
|| GET /help/:language/page/admin/courseManagement/addUserE.png|
|| GET /help/:language/page/admin/courseManagement/grantRights.md|
|| GET /help/:language/page/admin/courseManagement/grantRightsA.png|
|| GET /help/:language/page/admin/courseManagement/grantRightsB.png|
|| GET /help/:language/page/admin/courseManagement/grantRightsC.png|
|| GET /help/:language/page/admin/courseManagement/grantRightsD.png|
|| GET /help/:language/page/admin/courseManagement/plugins.md|
|| GET /help/:language/page/admin/courseManagement/pluginsA.png|
|| GET /help/:language/page/admin/courseManagement/pluginsB.png|
|| GET /help/:language/page/admin/courseManagement/pluginsC.png|
|| GET /help/:language/page/admin/courseManagement/addExerciseType.md|
|| GET /help/:language/page/admin/courseManagement/addExerciseTypeA.png|
|| GET /help/:language/page/admin/courseManagement/addExerciseTypeB.png|
|| GET /help/:language/page/admin/courseManagement/addExerciseTypeC.png|
|| GET /help/:language/page/admin/courseManagement/editExerciseType.md|
|| GET /help/:language/page/admin/courseManagement/editExerciseTypeA.png|
|| GET /help/:language/page/admin/courseManagement/editExerciseTypeB.png|
|| GET /help/:language/page/admin/courseManagement/editExerciseTypeC.png|
|| GET /help/:language/page/admin/courseManagement/editExerciseTypeD.png|
|| GET /help/:language/setting/MaxStudentUploadSize/MaxStudentUploadSizeDesc.md|
|| GET /help/:language/setting/MaxStudentUploadSize/maxStudentUploadSizeA.png|
|| GET /help/:language/setting/MaxStudentUploadSize/maxStudentUploadSizeB.png|
|| GET /help/:language/setting/GenerateDummyCorrectionsForTutorArchives/GenerateDummyCorrectionsForTutorArchivesDesc.md|
|| GET /help/:language/page/common/accountSettings/password.md|
|| GET /help/:language/page/common/accountSettings/passwordA.png|
|| GET /help/:language/page/common/accountSettings/passwordB.png|
|| GET /help/:language/page/common/accountSettings/passwordC.png|
|| GET /help/:language/page/common/login/login.md|
|| GET /help/:language/page/common/login/loginA.png|
|| GET /help/:language/page/common/login/loginB.png|
|| GET /help/:language/page/common/login/loginC.png|
|| GET /help/:language/page/common/accountSettings/user.md|
|| GET /help/:language/page/common/accountSettings/userA.png|
|| GET /help/:language/page/common/accountSettings/userB.png|
|| GET /help/:language/page/common/accountSettings/userC.png|
|| GET /help/:language/page/common/accountSettings/userD.png|
|| GET /help/:language/page/common/accountSettings/userE.png|
|| GET /help/:language/page/common/accountSettings/userF.png|
|| GET /help/:language/page/common/accountSettings/userG.png|
|| GET /help/:language/page/admin/condition/list.md|
|| GET /help/:language/page/admin/condition/listA.png|
|| GET /help/:language/page/admin/condition/listB.png|
|| GET /help/:language/page/admin/condition/listC.png|
|| GET /help/:language/page/admin/condition/listD.png|
|| GET /help/:language/page/admin/condition/set.md|
|| GET /help/:language/page/admin/condition/setA.png|
|| GET /help/:language/page/admin/condition/setB.png|
|| GET /help/:language/page/admin/condition/setC.png|
|| GET /help/:language/page/admin/condition/setD.png|
|| GET /help/:language/page/admin/condition/summary.md|
|| GET /help/:language/page/admin/condition/summaryA.png|
|| GET /help/:language/page/admin/condition/summaryB.png|
|| GET /help/:language/page/admin/condition/summaryC.png|
|| GET /help/:language/page/admin/condition/summaryD.png|
|| GET /help/:language/page/admin/condition/summaryE.png|
|| GET /help/:language/page/admin/condition/summaryF.png|
|| GET /help/:language/page/admin/condition/summaryG.png|
|| GET /help/:language/page/admin/condition/summaryH.png|
|| GET /help/:language/page/admin/condition/summaryI.png|
|| GET /help/:language/page/common/uploadHistory/change.md|
|| GET /help/:language/page/common/uploadHistory/changeA.png|
|| GET /help/:language/page/common/uploadHistory/changeB.png|
|| GET /help/:language/page/common/uploadHistory/changeC.png|
|| GET /help/:language/page/common/uploadHistory/changeD.png|
|| GET /help/:language/page/common/uploadHistory/changeE.png|
|| GET /help/:language/page/admin/uploadHistory/select.md|
|| GET /help/:language/page/admin/uploadHistory/selectA.png|
|| GET /help/:language/page/admin/uploadHistory/selectB.png|
|| GET /help/:language/page/admin/uploadHistory/selectC.png|
|| GET /help/:language/page/admin/uploadHistory/selectD.png|
|| GET /help/:language/page/admin/uploadHistory/selectE.png|
|| GET /help/:language/page/admin/studentMode/studentMode.md|
|| GET /help/:language/page/admin/studentMode/studentModeA.png|
|| GET /help/:language/page/admin/studentMode/studentModeB.png|
|| GET /help/:language/page/admin/studentMode/studentModeC.png|
|| GET /help/:language/page/admin/studentMode/studentModeD.png|
|| GET /help/:language/page/student/upload/upload.md|
|| GET /help/:language/page/student/upload/uploadA.png|
|| GET /help/:language/page/student/upload/uploadB.png|
|| GET /help/:language/page/student/upload/uploadC.png|
|| GET /help/:language/page/student/upload/uploadD.png|
|| GET /help/:language/page/student/upload/uploadE.png|
|| GET /help/:language/page/student/upload/uploadF.png|
|| GET /help/:language/page/student/upload/uploadG.png|
|| GET /help/:language/page/student/upload/uploadH.png|
|| GET /help/:language/page/student/student/student.md|
|| GET /help/:language/page/student/student/sheetHeadA.png|
|| GET /help/:language/page/student/student/sheetHeadB.png|
|| GET /help/:language/page/student/student/sheetHeadC.png|
|| GET /help/:language/page/student/student/sheetHeadD.png|
|| GET /help/:language/page/student/student/sheetHeadE.png|
|| GET /help/:language/page/student/student/sheetBodyA.png|
|| GET /help/:language/page/student/student/sheetBodyB.png|
|| GET /help/:language/page/student/student/sheetBodyC.png|
|| GET /help/:language/page/student/student/sheetBodyD.png|
|| GET /help/:language/page/student/student/sheetBodyE.png|
|| GET /help/:language/page/student/student/sheetBodyF.png|
|| GET /help/:language/page/student/student/sheetBodyG.png|
|| GET /help/:language/page/student/student/sheetBodyH.png|
|| GET /help/:language/page/student/student/sheetBodyI.png|
|| GET /help/:language/page/student/student/sheetBodyJ.png|
|| GET /help/:language/page/student/student/navigationA.png|
|| GET /help/:language/page/student/student/navigationB.png|
|| GET /help/:language/page/student/student/navigationC.png|
|| GET /help/:language/page/student/student/navigationD.png|
|| GET /help/:language/page/student/student/navigationE.png|
|| GET /help/:language/page/student/student/navigationF.png|
|| GET /help/:language/page/student/student/navigationG.png|
|| GET /help/:language/page/common/courseSelect/courseSelect.md|
|| GET /help/:language/page/common/courseSelect/courseSelectA.png|
|| GET /help/:language/page/common/courseSelect/courseSelectB.png|
|| GET /help/:language/page/common/courseSelect/courseSelectC.png|
|| GET /help/:language/page/admin/faq/faq.md|
|| GET /help/:language/page/admin/faq/faqA.png|
|| GET /help/:language/page/admin/faq/faqB.png|
|| GET /help/:language/page/admin/faq/faqC.png|
|| GET /help/:language/page/student/faq/faq.md|
|| GET /help/:language/page/tutor/faq/faq.md|


Stand 25.07.2017
