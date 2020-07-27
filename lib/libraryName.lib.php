<?php 
/**
 * @param libraryName - MODULE LIBRARY
 * this param as been same at file name
 */
class libraryName
{
    private $tbl;
    public $label_module;

    /**
     * Constructor
     * @param int $userId
     */
    function __construct()
    {
        $this->tbl          = get_module_main_tbl(array('MODULE_TABLE'));
        $this->userId       = claro_get_current_user_id();
        $this->label_module = "MODULE_CODE";
    }
}