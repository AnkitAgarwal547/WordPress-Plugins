<?php

function DSview_survey_list(){

	global $wpdb;
	$table = $wpdb->prefix.'d_categories_surveys';
    $results = $wpdb->get_results( "SELECT * FROM $table" );
?>
<div class="wrap container">
	<h1 class="wp-heading-inline">Add Category</h1>
	<div id="poststuff" class="container">
		<!-- <form id="survey-form-cat"> -->
		<div id="col-left" class="wp-hidden-children">
			<!-- <a id="category-add-toggle" href="#category-add" class="hide-if-no-js taxonomy-add-new">+ Add New Category</a> -->
			<form id="category-add-submit">
				<!-- <p id="category-add" class="category-add wp-hidden-child"> -->
				<div class="form-field form-required term-name-wrap">
					<label class="" for="newcategory">Add New Category</label><br>
					<input type="text" name="newcategory" placeholder="Enter Category Name" id="newcategory"
						class="form-required form-input-tip" value="" aria-required="true">
				</div>
				<br>
				<div class="form-field term-parent-wrap">
					<label class="" for="parent_cat">Select Parent</label><br>
					<?php if($results) { ?>
					<select name="parent_cat" id="parent_cat">
						<option value="0">None</option>
						<?php foreach($results as $result){ ?>
						<option value="<?= $result->id ?>"><?= $result->category_name ?></option>
						<?php } ?>
					</select>
					<?php } ?>
				</div>
				<br>
				<!-- <div class="form-field term-parent-wrap">
					<label class="" for="category_icon">Category Icon</label><br>
					<input type="file" name="file" id="category_icon" class="form-control" accept="image/x-png,image/gif,image/jpeg">
					<br>
					<img src="" id="category-icon-preview">
					<p id="b64" style="display: none;"></p>
				</div> -->
				<!-- <br> -->
				<div class="form-field term-parent-wrap">
					<button class="button button-primary add-cat">Add New Category <i
							class="fa fa-spin fa-spinner dean-loader" style="display:none"></i></button>
				</div>
				<!-- </p> -->
			</form>
		</div>
		<div id="col-right" class="category-display">
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<th>Category Name</th>
						<th>Category Type</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if($results) { ?>
					<?php foreach($results as $result){ ?>
					<tr>
						<td><?= $result->category_name ?></td>
						<td>
							<?php
							if($result->parent == 0){
								echo 'Parent';
							}
							else{
								foreach($results as $parent_cat){
									if($result->parent == $parent_cat->id){
										echo $parent_cat->category_name;
									}
								}
							}
						?>

						</td>
						<td>
							<button class="button button-secondary delete-category" data-id="<?= $result->id ?>"
								parent-id="<?= $result->parent ?>">Delete <i class="fa fa-spin fa-spinner dean-loader"
									style="display:none"></i></button>
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>


<script>
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<?php
}

// ---------------------------------------------------------------------------------
// Ajax For saving the survey Category Data to Database

add_action( 'wp_ajax_create_cat_dean', 'dean_create_category_save', 0 );
// add_action( 'wp_ajax_nopriv_create_cat_dean', 'dean_create_category_save' );
function dean_create_category_save(){

	$formData = $_POST['cat_name'];
	$parent_cat = $_POST['parent_cat'];
	// $file = $_POST['file'];
	// print_r($file);
	// die();
	
	if(empty($parent_cat)){
		$parent_cat = 0;
	}

    $user_id = get_current_user_id();
    global $wpdb;
    $table = $wpdb->prefix.'d_categories_surveys';

    $results = $wpdb->get_results( "SELECT * FROM $table WHERE category_name = '$formData'");
    if(count($results) > 0){
        wp_send_json(array('status'=> 'failed', 'msg' => 'This category already exist'));
        exit;
    }
    $data = array( 'category_name' => $formData, 'parent' => $parent_cat, 'user_id' => $user_id, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'));
    $wpdb->insert($table,$data);
    // $my_id = $wpdb->insert_id;
    wp_send_json(array('status'=> 'success', 'msg' => 'Category added Successfully'));
    exit;
}

// -----------------------------------------------------------------------------------
// Deleting Category
add_action( 'wp_ajax_delete_cat_dean', 'dean_delete_category_save', 0 );
// add_action( 'wp_ajax_nopriv_create_cat_dean', 'dean_create_category_save' );
function dean_delete_category_save(){

	$cat_id = $_POST['cat_id'];
	$parent_id = $_POST['parent_id'];

    $user_id = get_current_user_id();
    global $wpdb;
    $table = $wpdb->prefix.'d_categories_surveys';

	// children category deletion if Top parent deleted
	if($parent_id == 0){
		$results = $wpdb->get_results( "SELECT * FROM $table WHERE id = '$cat_id'");
		if(count($results) > 0){
			$id = $results[0]->id;
			$delete_child = $wpdb->query( 
				$wpdb->prepare( 
					"DELETE FROM $table WHERE parent = $id"
				)
			);
		}
	}
	
	// Parent Category Delete
	$category = $wpdb->query( 
        $wpdb->prepare( 
            "DELETE FROM $table WHERE id = $cat_id"
        )
    );
    if($category){
		$table = $wpdb->prefix.'d_categories_default_questions'; 
		$delete_default = $wpdb->query( 
			$wpdb->prepare( 
				"DELETE FROM $table WHERE cat_id = $cat_id"
			)
		);
		wp_send_json(array('status'=> 'success', 'msg' => 'Category deleted Successfully'));
		exit;
    }
}