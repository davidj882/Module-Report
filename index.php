<?php
/**
    * MODULE_CODE report
    * %TITLE FORM MODULE% 
    * 
    * @version     1.0
    * @author      Developer Name <Developer Email>
    * @license     http://www.gnu.org/copyleft/gpl.html
    *              GNU GENERAL PUBLIC LICENSE version 2.0
    * @package     MODULE_CODE
    *
    */
/*=====================================================================
   Initialisation
=====================================================================*/

// Module identifier (aka Module Label)
$tlabelReq = 'MODULE_CODE';

// Include DaVinci Kernel
require '../../davinci/inc/claro_init_global.inc.php';

/*=====================================================================
   Security Check
=====================================================================*/
if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

/*=====================================================================
   Library
=====================================================================*/
FromKernel::uses('display/layout.lib','display/breadcrumbs.lib');
From::Module($tlabelReq)->uses('libraryName.lib');

/*=====================================================================
    Config
=====================================================================*/
$moduleName = claro_get_module_name($tlabelReq);
$nameTools  = ucfirst( get_lang($moduleName) );
//$menuUser   = claro_html_menu_horizontal($userMenu);
$banner     = claro_html_tool_title($nameTools);

/*=====================================================================
   Command
=====================================================================*/

/*=====================================================================
   Display section
=====================================================================*/
$template = new ModuleTemplate($tlabelReq, 'index.tpl.php');
$template->assign('cid', claro_get_current_course_id());
$template->assign('courseUserslist', get_courseUsersList());
$template->assign('filename', get_lang('General Report')."_".claro_get_current_course_id());

$davinci->display->body->appendContent($banner);
$davinci->display->body->appendContent($breadCrumbs);
$davinci->display->body->appendContent($template->render());
echo $davinci->display->render();
?>