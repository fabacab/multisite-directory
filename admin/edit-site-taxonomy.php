<?php
global $wpdb; //DB connection

//functions to display admin tables and forms
require_once('t4ns_functions.php');


if(isset($_POST['st_addtag_action']) && $_POST['st_addtag_action'] == 'Y' && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'add-t4ns-new')) {
    //new tag added 
    //error_log(print_r($_POST, true));
    $parent =-1;
    if(isset($_POST['parent'])) $parent = $_POST['parent'];
    
    //set up values and formats
    $values = array('name'=> $_POST['tag-name']);
    $format = array('%s');
    $values['taxonomy']=$_POST['taxonomy'];
    $format[]=('%s');
    if($parent > 0){
        $values['parent']=$parent;
        $format[]='%d';
    }

    $values['description'] = $_POST['description'];
    $format[]='%s';
 
     
    $wpdb->insert( $table_terms, $values, $format);
    $newTerm = $wpdb->insert_id;
    
        
}
//edit a term
if(isset($_POST['st_edittag_action']) && 'Y' === $_POST['st_edittag_action'] && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'edit-t4ns-'.$_POST['term_id'])) {
        
        $parent =-1;
        if(isset($_POST['parent'])) $parent = $_POST['parent'];
        
        //set up values and formats
        $values = array('name'=> $_POST['tag-name']);
        $format = array('%s');
        //taxonomy
        $values['taxonomy']=$_POST['taxonomy'];
        $format[]=('%s');
        //parent if set
        if($parent > 0){
            $values['parent']=$parent;
            $format[]='%d';
        }
        //description
        $values['description'] = $_POST['description'];
        $format[]='%s';
        
        $row=array('term_id'=>$_POST['term_id']);
        $rowFormat = array( '%d' );
        
        $wpdb->update( $table_terms, $values, $row, $format, $rowFormat);
        
}
//delete a term
if(isset($_GET['action']) && 'delete' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete-t4ns-'.$_GET['term_id'])) {
        
        $row=array('term_id'=>$_GET['term_id']);
        $rowFormat = array( '%d' );
        
        $wpdb->delete( $table_terms, $row, $rowFormat);
        
}
?>
<div class="wrap">
    <?php
    if(isset($_GET['action']) && 'edit'===$_GET['action']): //edit a term
        $submit = __('Edit Category', 'sitesTax_syllogic_in' );
        echo "<h2>" .$submit. "</h2>";
        t4ns_term_form($submit,'N',$taxonomy);
    else:
    echo "<h2>" . __( 'Sites Category', 'sitesTax_syllogic_in' ) . "</h2>"; ?>
    <div id="col-right">
        <div class="col-wrap">
            <form id="posts-filter" action="" method="post">
                <input name="taxonomy" value="<?php echo $taxonomy;?>" type="hidden">
                <input name="st_edittag_action" value="Y" type="hidden">
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text"><?php echo __('Select bulk action', 'sitesTax_syllogic_in');?></label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1" selected="selected"><?php echo __('Bulk Actions', 'sitesTax_syllogic_in');?></option>
                            <option value="delete"><?php echo __('Delete', 'sitesTax_syllogic_in');?></option>
                        </select>
                        <input name="" id="doaction" class="button action" value="<?php echo __('Apply', 'sitesTax_syllogic_in');?>" type="submit">
                    </div>
                    <div class="tablenav-pages one-page">
                        <span class="displaying-num">0 items</span>
                        <span class="pagination-links">
                            <a class="first-page disabled" title="<?php echo __('Go to the first page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=category">Ç</a>
                            <a class="prev-page disabled" title="<?php echo __('Go to the previous page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=category&amp;paged=1">Ü</a>
                            <span class="paging-input">
                                <label for="current-page-selector" class="screen-reader-text"><?php echo __('Select Page', 'sitesTax_syllogic_in');?></label>
                                <input class="current-page" id="current-page-selector" title="Current page" name="paged" value="1" size="1" type="text"> of <span class="total-pages">1</span>
                            </span>
                            <a class="next-page disabled" title="<?php echo __('Go to the next page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=sites_category&amp;paged=1">Ý</a>
                            <a class="last-page disabled" title="<?php echo __('Go to the last page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=sites_category&amp;paged=1">È</a></span>
                    </div>
                    <br class="clear">
                </div>
                <table class="wp-list-table widefat fixed tags">
                    <thead>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                                <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                <input id="cb-select-all-1" type="checkbox">
                            </th>
                            <th scope="col" id="name" class="manage-column column-name sortable desc" style="">
                                <span>Name</span>
                            </th>
                            <th scope="col" id="description" class="manage-column column-description sortable desc" style="">
                                <span>Description</span>
                            </th>
                            <th scope="col" id="posts" class="manage-column column-posts num sortable desc" style="">
                                <span>Count</span>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th scope="col" class="manage-column column-cb check-column" style="">
                                <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                                <input id="cb-select-all-2" type="checkbox">
                            </th>
                            <th scope="col" class="manage-column column-name sortable desc" style="">
                                <span>Name</span>
                            </th>
                            <th scope="col" class="manage-column column-description sortable desc" style="">
                                <span>Description</span>
                            </th>
                            <th scope="col" class="manage-column column-posts num sortable desc" style="">
                                <span>Count</span>
                            </th>
                        </tr>
                    </tfoot>
                    <tbody id="the-list" data-wp-lists="list:tag">
                <?php t4ns_term_row(0, $taxonomy); ?>
                    </tbody>
                </table>
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php echo __('Select bulk action', 'sitesTax_syllogic_in');?></label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1" selected="selected"><?php echo __('Bulk Actions', 'sitesTax_syllogic_in');?></option>
                            <option value="delete"><?php echo __('Delete', 'sitesTax_syllogic_in');?></option>
                        </select>
                        <input name="" id="doaction" class="button action" value="<?php echo __('Apply', 'sitesTax_syllogic_in');?>" type="submit">
                    </div>
                    <div class="tablenav-pages one-page">
                        <span class="displaying-num">0 items</span>
                        <span class="pagination-links">
                            <a class="first-page disabled" title="<?php echo __('Go to the first page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=category">Ç</a>
                            <a class="prev-page disabled" title="<?php echo __('Go to the previous page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=category&amp;paged=1">Ü</a>
                            <span class="paging-input">
                                <label for="current-page-selector" class="screen-reader-text"><?php echo __('Select Page', 'sitesTax_syllogic_in');?></label>
                                <input class="current-page" id="current-page-selector" title="Current page" name="paged" value="1" size="1" type="text"> of <span class="total-pages">1</span>
                            </span>
                            <a class="next-page disabled" title="<?php echo __('Go to the next page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=sites_category&amp;paged=1">Ý</a>
                            <a class="last-page disabled" title="<?php echo __('Go to the last page', 'sitesTax_syllogic_in');?>" href="./sites_taxonomy.php?taxonomy=sites_category&amp;paged=1">È</a></span>
                    </div>
                    <br class="clear">
                </div>
                <br class="clear">
            </form>
            <div class="form-wrap"></div>
        </div>
    </div><!-- /col-right -->

    <div id="col-left">
        <div class="col-wrap">
            <div class="form-wrap">
                <h3><?php echo $submit = __('Add New Category', 'sitesTax_syllogic_in' );?></h3>
                <?php t4ns_term_form($submit,'Y',$taxonomy);?>
            </div>
        
        </div>
    </div><!-- /col-left -->
<?php endif;?>
</div>