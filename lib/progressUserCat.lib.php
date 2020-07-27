<?php 
/**
 * @param libraryName - MODULE LIBRARY
 * this param as been same at file name
 */
class libraryName
{
    private $tbl;
    private $codeCategory;
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

    /**
     * Common getter for $userReportList
     */
    public function getUserProgressCat($category_id)
    {
        /// GET COURSES CAT
        $coursesList    = $this->get_courses_by_cat($category_id);
        $total_courses  = count($coursesList);

        if ($total_courses > 0) {

            foreach ($coursesList as $c) {
                $dbName         = $c['dbName']."_";
                $last_module    = $this->get_last_module($dbName);

                if (!empty($last_module)) {
                    $progress2 = $this->get_progress($c['directory'], $last_module['learnPath_id'], $last_module['learnPath_module_id']);
                    $total_progress += $progress2;
                }
            }

            $progress_cat = $this->get_progress_percent($total_progress, $total_courses);

            $userProgress = $progress_cat;

        }else{
            $userProgress = -1;
        }

        return $userProgress;
    }

    public function get_last_module($dbName)
    {
        $tbl_cdb_names = claro_sql_get_course_tbl($dbName);

        $tbl_lp_learnPath            = "`".$tbl_cdb_names['lp_learnPath']."`";
        $tbl_lp_rel_learnPath_module = "`".$tbl_cdb_names['lp_rel_learnPath_module']."`";
        $tbl_lp_user_module_progress = "`".$tbl_cdb_names['lp_user_module_progress']."`";
        $tbl_lp_module               = "`".$tbl_cdb_names['lp_module']."`";

        $sql = "SELECT
                    lpm.learnPath_id, lpm.module_id, lpm.learnPath_module_id, m.contentType
                FROM
                    $tbl_lp_rel_learnPath_module AS lpm
                INNER JOIN $tbl_lp_learnPath AS lp ON lp.learnPath_id = lpm.learnPath_id
                INNER JOIN $tbl_lp_module AS m ON m.module_id = lpm.module_id
                ORDER BY lp.rank DESC, lpm.rank DESC LIMIT 1";

        return claro_sql_query_fetch_single_row($sql);
    }

    public function get_progress($course_code, $learnPath_id, $module_id, $type_module = null)
    {
        $tbl_graduated = "`".$this->tbl['graduated']."`";

        $sql = "SELECT count(user_id) AS is_graduated FROM $tbl_graduated WHERE course_code = '$course_code' AND learnPath_id = $learnPath_id AND learnPath_module_id = $module_id AND user_id = ".$this->userId;
        $course_progress = (claro_sql_query_fetch_single_value($sql) > 0 ) ? true : false;

        return $course_progress;
    }

    public function get_progress_percent($todo, $total)
    {
        return round(($todo*100)/$total, 1);
    }

    public function get_courses_by_cat($categoryId)
    {
        $tbl_courses                = "`".$this->tbl['cours']."`";
        $tbl_category               = "`".$this->tbl['category']."`";
        $tbl_rel_course_user        = "`".$this->tbl['rel_course_user']."`";
        $tbl_rel_course_category    = "`".$this->tbl['rel_course_category']."`";
        
        //// GET COURSES CATEGRY
        /*$sql = "SELECT * FROM $tbl_courses c INNER JOIN $tbl_rel_course_category cc ON c.cours_id = cc.courseId INNER JOIN $tbl_category ca ON ca.id = cc.categoryId INNER JOIN $tbl_rel_course_user cu ON cu.code_cours = c.administrativeNumber WHERE cc.categoryId = $categoryId AND cu.user_id =".claro_get_current_user_id();
        
        $coursesList = claro_sql_query_fetch_all($sql);*/

        if (!claro_is_platform_admin()) {
            $sql = "SELECT * FROM $tbl_courses c INNER JOIN $tbl_rel_course_category cc ON c.cours_id = cc.courseId INNER JOIN $tbl_category ca ON ca.id = cc.categoryId INNER JOIN $tbl_rel_course_user cu ON cu.code_cours = c.administrativeNumber WHERE cc.categoryId = $categoryId AND cu.user_id =".claro_get_current_user_id();
        }else{
            $sql = "SELECT * FROM $tbl_courses c INNER JOIN $tbl_rel_course_category cc ON c.cours_id = cc.courseId INNER JOIN $tbl_category ca ON ca.id = cc.categoryId WHERE cc.categoryId = $categoryId";
        }

        return claro_sql_query_fetch_all($sql);;
    }

    //// OLD
    public function get_all_categories_by_user($parent = 0, $level = 0, $visibility = null, $canHaveCoursesChild = null, $code = null)
    {
        // Get table name
        $tbl_mdb_names              = claro_sql_get_main_tbl();
        $tbl_category               = "`".$tbl_mdb_names['category']."`";
        $tbl_course                 = "`".$tbl_mdb_names['course']."`";
        $tbl_rel_course_user        = "`".$tbl_mdb_names['rel_course_user']."`";
        $tbl_rel_course_category    = "`".$tbl_mdb_names['rel_course_category']."`";
        
        // Retrieve all children of the id $parent
        $sql = "SELECT
                    COUNT( cc.courseId ) AS nbCourses,
                    ca.id,
                    ca.`name`,
                    ca.`code`,
                    ca.idParent,
                    ca.rank,
                    ca.visible,
                    ca.canHaveCoursesChild,
                    co.intitule AS dedicatedCourse,
                    co.`code` AS dedicatedCourseCode 
                FROM
                    $tbl_rel_course_user cu 
                    INNER JOIN $tbl_course co ON co.directory = cu.code_cours
                    INNER JOIN $tbl_rel_course_category cc ON cc.courseId = co.cours_id
                    INNER JOIN $tbl_category ca ON ca.id = cc.categoryId
                WHERE
                    ca.idParent = $parent 
                    AND cu.user_id = ".$this->userId;
        
        if ( !is_null($visibility) )
        {
            $sql .= " AND ca.visible = " . $visibility;
        }

        if (!is_null($canHaveCoursesChild)) {
            $sql .= " AND ca.canHaveCoursesChild = " . $canHaveCoursesChild;
        }

        if (!is_null($code)) {
            if (is_array($code)) {
                $codes = "'".implode("','", $code)."'";
                $sql .= " AND ca.`code` IN ($codes)";
            }else{
                $sql .= " AND ca.`code` = '".$code."'";
            }
        }
        
        $sql .=  " GROUP BY ca.`id`";
        
        if ( get_conf('categories_order_by') == 'rank' )
            $sql .= "
                ORDER BY ca.`rank`";
        elseif ( get_conf('categories_order_by') == 'alpha_asc' )
            $sql .= "
                ORDER BY ca.`name` ASC";
        elseif ( get_conf('categories_order_by') == 'alpha_desc' )
            $sql .= "
                ORDER BY ca.`name` DESC";

        $result = davinci::getDatabase()->query($sql);
        $result_array = array();
        
        // Get each child
        foreach ( $result as $row )
        {
            $row['level'] = $level;
            $result_array[] = $row;
            // Call this function again to get the next level of the tree
            $result_array = array_merge( $result_array, claro_get_all_categories($row['id'], $level+1) );
        }
        
        return $result_array;
    }

    public function get_categories_dvcatpro()
    {

        $user_id                    = $this->userId;
        $cat_codes                  = $this->codeCategory;
        $tbl_mdb_names              = claro_sql_get_main_tbl();
        $tbl_category               = "`".$tbl_mdb_names['category']."`";
        $tbl_course                 = "`".$tbl_mdb_names['course']."`";
        $tbl_rel_course_user        = "`".$tbl_mdb_names['rel_course_user']."`";
        $tbl_rel_course_category    = "`".$tbl_mdb_names['rel_course_category']."`";

        if (is_array($cat_codes)) {
            $cat_codes = "'".implode("','", $cat_codes)."'";
        }

        $sql = "SELECT
                    ca.id,
                    ca.`name`,
                    ca.`code`,
                    ca.idParent,
                    ca.rank,
                    ca.visible,
                    ca.description,
                    ca.canHaveCoursesChild,
                    count(c.`code`) AS nbCourses
                FROM
                    $tbl_rel_course_user cu
                    INNER JOIN $tbl_course c ON cu.code_cours = c.`code`
                    INNER JOIN $tbl_rel_course_category rcca ON rcca.courseId = c.cours_id
                    INNER JOIN $tbl_category ca ON rcca.categoryId = ca.id 
                WHERE
                    user_id = $user_id 
                AND ca.`code` IN ($cat_codes)
                GROUP BY ca.`id`
                ORDER BY ca.`rank`";

        return claro_sql_query_fetch_all($sql);
    }


    public function get_all_categories_dvcatpro($status = null)
    {
        $tbl_mdb_names              = claro_sql_get_tbl(array('category', 'categories_desktop', 'rel_course_category'));
        $tbl_category               = "`".$tbl_mdb_names['category']."`";
        $tbl_categories_desktop     = "`".$tbl_mdb_names['categories_desktop']."`";
        $tbl_rel_course_category    = "`".$tbl_mdb_names['rel_course_category']."`";

        if (!is_null($status)) {
            $conditional = " WHERE `status` = ".$status;
        } else {
            $conditional = "";
        }
        
        $sql = "SELECT
                        c.*,
                        cd.*,
                        COUNT(*) AS nbCourses
                    FROM
                        $tbl_category c
                        LEFT JOIN $tbl_categories_desktop cd ON c.id = cd.cat_id
                        LEFT JOIN $tbl_rel_course_category rc ON rc.categoryId = c.id
                    $conditional
                    GROUP BY c.`id`
                    ORDER BY c.`rank`";

        return claro_sql_query_fetch_all($sql);
    }

    public function saveCatPro($categoryId, $action)
    {
        $is_register                = $this->is_register($categoryId);
        $tbl_mdb_names              = claro_sql_get_tbl(array('categories_desktop'));
        $tbl_categories_desktop     = "`".$tbl_mdb_names['categories_desktop']."`";
        $today                      = date('Y-m-d H:i:s'); 
        
        if (!$is_register) {
            $sql = "INSERT INTO $tbl_categories_desktop SET `cat_id` = '$categoryId', `status` = 1, `date_register` = '$today'";

            if( claro_sql_query($sql) ) {
                $alert = 0;
            } else {
                $alert = 1;
            }
        } else {
            $sql = "UPDATE $tbl_categories_desktop SET `status` = '$action', `date_update` = '$today' WHERE `cat_id` = '$categoryId'";

            if( claro_sql_query($sql) ) {
                if ($action) {
                    $alert = 2;
                } else {
                    $alert = 3;
                }
            } else {
                if ($action) {
                    $alert = 4;
                } else {
                    $alert = 5;
                }
            }
        }
        
        return $alert;
    }

    public function is_register($categoryId)
    {
        $tbl_mdb_names              = claro_sql_get_tbl(array('categories_desktop'));
        $tbl_categories_desktop     = "`".$tbl_mdb_names['categories_desktop']."`";

        $sql = "SELECT
                    count(*) as exist
                FROM
                    $tbl_categories_desktop cd 
                WHERE cd.cat_id = ".$categoryId;

        return claro_sql_query_get_single_value($sql);
    }
}