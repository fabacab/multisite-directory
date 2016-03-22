<?php
/*
 * Function to add/edit a network taxonomy term
 */
function t4ns_term_form($submit,$isAddTag='Y', $taxonomy){
    global $wpdb; //DB connection
    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;

    $name='';
    $parent='';
    $desc='';
    $term_id='';
    $formName = 'st_addtag_form';
    $formID = 'addtag';
    $termInput='';
    $actionName = 'st_addtag_action';
    $nonceField ='add-t4ns-new';
    
    $terms = $wpdb->get_results("SELECT
                   term_id, name, description, parent, count
                   FROM {$table_terms}
                   WHERE taxonomy = '{$taxonomy}'"
);
    $term ='';
    if('N'=== $isAddTag && isset($_GET['term_id']) ){
        $term_id =  $_GET['term_id'];
        for($idx=0;$idx<sizeof($terms);$idx++) if($term_id == $terms[$idx]->term_id) break;
        $term = $terms[$idx];
        array_splice($terms,$idx,1); //this will remove itself from the parent list
        $name = $term->name;
        $desc = $term->description;
        $parent = $term->parent;
        $formName = 'st_edittag_form';
        $actionName = 'st_edittag_action';
        $formID = 'edittag';
        $nonceField = 'edit-t4ns-'.$term_id;
        $termInput = '<input name="term_id" value="'.$term_id.'" type="hidden">';
    }
    ?>
    <form name="<?php echo $formName;?>" id="<?php echo $formID;?>" method="post" action="<?php echo network_admin_url('/sites.php?page=sites-categories'); ?>" class="validate">
        <input name="<?php echo $actionName;?>" value="Y" type="hidden">
        <input name="screen" value="edit-category" type="hidden">
        <input name="taxonomy" value="<?php echo $taxonomy;?>" type="hidden">
        <?php
        wp_nonce_field( $nonceField );
        echo $termInput;
        ?>

        <div class="form-field form-required term-name-wrap">
            <label for="tag-name"><?php echo __('Name', 'sitesTax_syllogic_in');?></label>
            <input name="tag-name" id="tag-name" value="<?php echo $name;?>" size="40" aria-required="true" type="text">
            <p><?php echo __('The name for this category.', 'sitesTax_syllogic_in');?></p>
        </div>
        <div class="form-field term-parent-wrap">
            <label for="parent"><?php echo __('Parent', 'sitesTax_syllogic_in');?></label>
            <select name="parent" id="parent" class="postform">
                <option value="-1"><?php echo __('None', 'sitesTax_syllogic_in');?></option>
        <?php
            foreach($terms as $term){ ?>
                <option value="<?php echo $term->term_id;?>" <?php echo ($parent==$term->term_id) ? 'selected':''; ?>><?php echo $term->name;?></option>
        <?php } ?>
            </select>
            <p><?php echo __('Sites Categories can have a hierarchy. You might have a Language category, and under that have children categories for English and French. Totally optional.', 'sitesTax_syllogic_in');?></p>
            </div>
        <div class="form-field term-description-wrap">
            <label for="tag-description"><?php echo __('Description', 'sitesTax_syllogic_in');?></label>
            <textarea name="description" id="tag-description" rows="5" cols="40"><?php echo $desc;?></textarea>
            <p><?php echo __('The description is optional.', 'sitesTax_syllogic_in');?></p>
        </div>
    
        <p class="submit"><input name="submit" id="submit" class="button button-primary" value="<?php echo $submit; ?>" type="submit"></p>
    </form>
    <?php
}
/*
 *  Function to display the rows of network taxonomy terms and their children
 */
function t4ns_term_row($parent, $taxonomy,$level='&nbsp;'){
    global $wpdb; //DB connection
    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;

    //$wpdb->flush(); //flsuh previous query    
    $terms = $wpdb->get_results("SELECT
                   term_id, name, description, parent, count
                   FROM {$table_terms}
                   WHERE taxonomy = '{$taxonomy}' AND parent= '{$parent}'"
    );
    if(!$terms) return;
    //error_log("T4NS: Found ".$wpdb->num_rows." terms");
    //error_log("T4NS: for query: ".$wpdb->last_query);
    $idx=0;
    $queryArgs = array('page'=>'sites-categories');
    foreach($terms as $term){
        $idx+=1;
        $queryArgs['term_id']=$term->term_id;
        ?>
        <tr id="tag-<?php echo $term->term_id;?> class="<?php (0 == ($idx % 2)) ? '':'alternate';?>">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-<?php echo $term->term_id;?>">Select <?php echo $term->name;?></label>
                <input name="delete_tags[]" value="<?php echo $term->term_id;?>" id="cb-select-<?php echo $term->term_id;?>" type="checkbox">
            </th>
            <td class="name column-name">
                <strong><a class="row-title" href="<?php echo add_query_arg($queryArgs,network_admin_url('/sites.php')); ?>?" title="Edit “<?php echo $term->name;?>”"><?php echo $level.$term->name;?></a></strong><br>
                <div class="row-actions">
                    <?php
                    $queryArgs['action']='edit';
                    $editUrl = wp_nonce_url( add_query_arg($queryArgs,network_admin_url('/sites.php')), 'edit-t4ns-'.$term->term_id );
                    ?>
                    <span class="edit"><a href="<?php echo add_query_arg($queryArgs,network_admin_url('/sites.php')); ?>">Edit</a> | </span>
                    <span class="inline hide-if-no-js"><a href="#" class="editinline">Quick&nbsp;Edit</a> | </span>
                    <?php
                    $queryArgs['action']='delete';
                    $editUrl = wp_nonce_url( add_query_arg($queryArgs,network_admin_url('/sites.php')), 'delete-t4ns-'.$term->term_id );
                    ?>
                    <span class="delete"><a class="delete-tag" href="<?php echo $editUrl;?>">Delete</a> | </span>
                </div>
                <div class="hidden" id="inline_<?php echo $term->term_id;?>">
                    <div class="name"><?php echo $term->name;?></div>
                    <div class="parent"><?php echo $term->parent;?></div>
                </div>
            </td>
            <td class="description column-description"><?php echo $term->description;?></td>
            <td class="posts column-posts"><?php echo $term->count;?></td>
        </tr>
        <?php
        //let's display the child terms
        t4ns_term_row($term->term_id, $taxonomy,'&mdash;'.$level);
    }
}
/*
 * Code injection using jQuery in to site-new.php network admin page
 */
function t4ns_wp_site_new(){
    $taxonomy = T4NS_TAXONOMY_CATEGORY;
    ob_start();
    ?>
    <tr class="form-field">
        <th scope="row">Site Categories</th>
        <td>
            <ul>
            <?php t4ns_list_terms(0,$taxonomy,0);?>
            </ul>
        </td>
    </tr>
    <?php
    $template = ob_get_contents();
		// An FTP client might have changed the \n to \r\n.
		$template = str_replace( array ("\n", "\r", "'" ), array( '', '', "\\'" ), $template );
		ob_end_clean();
		?>
		<script>
			( function( $ ) {
				$(document).ready( function(){

					var lastRow      = $( 'form table:first-of-type tbody tr:last-child' ),
						template    = '<?php echo $template; ?>'
					;

					//lastRow.after( template );

				} );
			} )( jQuery );
		</script>
		<?php
		
}
function t4ns_list_terms($parent,$taxonomy,$level=0){
    global $wpdb; //DB connection
    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
 
    $terms = $wpdb->get_results("SELECT
                   term_id, name, description, parent, count
                   FROM {$table_terms}
                   WHERE taxonomy = '{$taxonomy}' AND parent= '{$parent}'"
    );
    if(!$terms) return;
    //error_log("T4NS: Found ".$wpdb->num_rows." terms");
    //error_log("T4NS: for query: ".$wpdb->last_query);
    if($level>0) echo '<ul class="children" style="margin-left:18px;">';
    foreach($terms as $term){
        ?>
        <li class="sterm-<?php echo $term->term_id;?>">
            <label class="selectit"><input value="<?php echo $term->term_id;?>" name="st_site_terms[]" class="in-sterm-<?php echo $term->term_id;?>" type="checkbox"><?php echo $term->name;?></label>
        <?php t4ns_list_terms($term->term_id,$taxonomy,$level+1);?>
        </li>
        <?php
    }
    if($level>0) echo '</ul>';
}
/*
 * Store selected network terms for new site
 */
function t4ns_insert_site_terms( $blog_id ){
    global $wpdb; //DB connection
    $taxonomy = T4NS_TAXONOMY_CATEGORY;
    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
    $table_relation = $wpdb->prefix . T4NS_RELATION_TABLE;
    
    //insert relationship
    //error_log("T4NS: Am Here!");
    if(isset($_POST['st_site_terms']) && is_array($_POST['st_site_terms'])){
        $values = array('blog_id'=>$blog_id);
        $format = array('%d','%d');
        foreach($_POST['st_site_terms'] as $term) {
            //set up values and formats
            $values['term_id']=$term;
            $wpdb->insert( $table_relation, $values, $format);
            $newTerm = $wpdb->insert_id;
            //update count of terms table
            $count = $wpdb->get_var( "SELECT count FROM {$table_terms} WHERE taxonomy = '{$taxonomy}' AND term_id= '{$term}'" );
            $wpdb->update( $table_terms, array('count'=>$count+1), array('term_id'=>$term), $format, array( '%d' ));
        }
    }
}
function t4ns_update_site_terms() {
    global $wpdb; //DB connection
    $taxonomy = T4NS_TAXONOMY_CATEGORY;
    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
    $table_relation = $wpdb->prefix . T4NS_RELATION_TABLE;
    check_ajax_referer( 't4ns_update_site_terms_nonce', 'security' );
    
    if(isset($_POST['blog_id']) && isset($_POST['st_site_terms']) && is_array($_POST['st_site_terms'])){
        $blog_id = intval( $_POST['blog_id'] );
        $updateValues = $_POST['st_site_terms'];
        
        //let's get current snapshop of DB
        $dbValues = $wpdb->get_results( "SELECT term_id FROM {$table_relation} WHERE blog_id= '{$blog_id}'" );
        for( $idx=0; $idx<count($dbValues); $idx++ ){
            $term=$dbValues[$idx];
            if(false !=($udx = array_search($term->term_id,$updateValues)) ) {
                //this value can be popped from both arrays
                array_splice($updateValues,$udx,1);
                array_splice($dbValues,$idx,1);
            }
        }
        //now the remaining values in either arrays have to be updated back in the DB.
        //remove old values
        
        $values = array('blog_id'=>$blog_id);
        $format = array('%d','%d');
        foreach($dbValues as $term){
            $term_id=$term->term_id;
            $values['term_id']=$term_id;
            $wpdb->delete( $table_relation, $values, $format);

            //update count of terms table
            $count = $wpdb->get_var( "SELECT count FROM {$table_terms} WHERE taxonomy = '{$taxonomy}' AND term_id= '{$term_id}'" );
            $wpdb->update( $table_terms, array('count'=>$count-1), array('term_id'=>$term_id), $format, array( '%d' ));
        }
        
        //insert new values
        foreach($updateValues as $term){
            $term_id = $term['value']; 
            $values['term_id'] = $term_id;
            $wpdb->insert( $table_relation, $values, $format);
            $newTerm = $wpdb->insert_id;
            
            //update count of terms table
            $count = $wpdb->get_var( "SELECT count FROM {$table_terms} WHERE taxonomy = '{$taxonomy}' AND term_id= '{$term_id}'" );
            $wpdb->update( $table_terms, array('count'=>$count+1), array('term_id'=>$term_id), $format, array( '%d' ));
        }
        echo true; //return successful completion of update
    }else echo false; //missing data, cannot update the DB
  die(); // this is required to return a proper result
}
add_action( 'wp_ajax_t4ns_update_site_terms', 't4ns_update_site_terms' );
/*
 * Custom category column in Sites table
 */
function t4ns_register_sites_column($columns){
		$columns['t4ns_terms'] = __('Categories','sitesTax_syllogic_in');
		return $columns;
}
//sortable
function t4ns_register_sortable_column($columns){
		$columns['t4ns_terms'] = 't4ns_terms';
		return $columns;
}
function t4ns_blog_term_field($column, $blogid){
		if ($column == 't4ns_terms'){
        global $wpdb; //DB connection
        $taxonomy = T4NS_TAXONOMY_CATEGORY;
        $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
        $table_relation = $wpdb->prefix . T4NS_RELATION_TABLE;
        
        $terms = $wpdb->get_results("SELECT {$table_terms}.name, {$table_terms}.term_id
                        FROM {$table_terms}, {$table_relation}
                        WHERE {$table_terms}.term_id = {$table_relation}.term_id
                        AND {$table_terms}.taxonomy = '{$taxonomy}' 
                        AND {$table_relation}.blog_id = {$blogid}"
        );
        $field='';
        foreach($terms as $term) $field .= '<a href="#?term_id='.$term->term_id.'">'.$term->name.'</a>, ';
        echo $field;
		}
		return $column;
}
/*
 * Quick edit
 */
function t4ns_wp_site_list(){
    global $wpdb; //DB connection
    $taxonomy = T4NS_TAXONOMY_CATEGORY;
    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
    $table_relation = $wpdb->prefix . T4NS_RELATION_TABLE;
    ob_start();
    ?>
    <tr id="edit-site" class="alternative-row-temp inline-edit-row quick-edit-row inline-editor hide-row">
        <td colspan="5" class="all-columns">
            <ul>
            <?php t4ns_list_terms(0,$taxonomy,0);?>
            </ul>
            <p>
                <a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft">Cancel</a>
                <input id="_inline_edit" name="_inline_edit" value="2d057e955a" type="hidden">
                <a accesskey="s" href="#inline-edit" class="button-primary save alignright">Update</a>
                <span class="spinner"></span>
                <input name="post_view" value="list" type="hidden">
                <input name="screen" value="edit-post" type="hidden">
                <input name="blog_id" value="t4ns-blog-id" type="hidden">
                <span class="error" style="display:none"></span>
                <br class="clear">
            </p>
        </td>
    </tr>
    <?php
    $template = ob_get_contents();
		// An FTP client might have changed the \n to \r\n.
		$template = str_replace( array ("\n", "\r", "'" ), array( '', '', "\\'" ), $template );
		ob_end_clean();
    $ajax_nonce = wp_create_nonce( 't4ns_update_site_terms_nonce' );
		?>
		<script>
			( function( $ ) {
				$(document).ready( function(){

					var template    = '<?php echo $template; ?>',
          colCount = 0;
          $('tbody#the-list tr:nth-child(1) td').each(function () {
            if ($(this).attr('colspan')) {
              colCount += +$(this).attr('colspan');
            } else {
              colCount++;
            }
          });
          $('tbody#the-list tr:nth-child(1) th').each(function () {
            if ($(this).attr('colspan')) {
              colCount += +$(this).attr('colspan');
            } else {
              colCount++;
            }
          });
          template = template.replace(/colspan="5/i,'colspan="'+colCount);
          $( 'tbody#the-list tr' ).each(function(index,value){
            var link = $(this).find('a.edit').attr('href');
            var quickEdit='<span class="quick-edit"><a href="#" class="inline">Quick Edit</a> | </span>';
            if (link.length > 0) {
                var blogID = link.match(/id=[0-9]+/).toString().replace(/id=/,"");
                $(value).attr('id','site-'+blogID);
                var classVal = $(value).attr('class');
                /*set id of new row*/
                var newRow = template.replace(/id="edit-site/i,'id="edit-site-'+blogID);
                /*set class new row*/
                newRow = newRow.replace(/class="alternative-row-temp/i,'class="'+classVal);
                /*set blog id in hidden input field*/
                newRow = newRow.replace(/value="t4ns-blog-id/i,'value="'+blogID);
                $(this).find('div.row-actions > span.edit').after(quickEdit);
            } else $(value).attr('id','idx-'+index);

            $(value).after( newRow );
          });
					$('span.quick-edit a.inline').on('click', function(){
            var editRow = $(this).closest('tr');
            var pid = editRow.attr('id');
            editRow.hide();
            $(this).closest('tbody').find('#edit-'+pid).show();
            
          });
          $('a.button-secondary.cancel').on('click', function(){
            var editRow = $(this).closest('tr');
            var pid = editRow.attr('id').replace(/edit-/,"");
            editRow.hide();
            $(this).closest('tbody').find('#'+pid).show();
            
          });
          $('a.button-primary.save').on('click', function(){
            var editRow = $(this).closest('tr');
            var pid = editRow.attr('id').replace(/edit-/,"");
            var siteRow = $(this).closest('tbody').find('#'+pid);
            /*TODO pick up checked values and arrange them as comma separated list*/
            /*we need to update the back end through ajax*/
            var data = {
                action: 't4ns_update_site_terms',
                security: '<?php echo $ajax_nonce; ?>',
                st_site_terms: editRow.find(' ul li input[type=checkbox]:checked').serializeArray(),
                blog_id: editRow.find('p input[name=blog_id]').val()
              };
              /* since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php*/
              $.post( ajaxurl, data, function( response)  {
                if(1==response){
                    editRow.hide();
                    siteRow.show();
                    /*TODO update siteRow t4ns_coumn with checked terms*/
                }else{
                    var errMsg = editRow.find('p span.error');
                    errMsg.text('Unable to update, refresh page and try again.');
                    errMsg.show();
                }
              });
          });
          <?php
          $blogs =  wp_get_sites();
          foreach($blogs as $blog){
            $blog_id = $blog['blog_id'];
            $terms = $wpdb->get_results("SELECT {$table_terms}.term_id
                        FROM {$table_terms}, {$table_relation}
                        WHERE {$table_terms}.term_id = {$table_relation}.term_id
                        AND {$table_terms}.taxonomy = '{$taxonomy}' 
                        AND {$table_relation}.blog_id = {$blog_id}"
            );
            foreach($terms as $term){
                echo "$('tr#edit-site-".$blog_id." input.in-sterm-".$term->term_id."').prop( 'checked', true );";
            }
          }
        ?>
				} );
			} )( jQuery );
		</script>
    <style> .hide-row{display:none;}</style>
		<?php
		
}
?>