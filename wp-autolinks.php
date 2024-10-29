<?php
/*
 Plugin Name: AutoLinks
 Plugin URI: http://www.blogcube.org/
 Version: 1.0.1
 Author: <a href="http://www.blogcube.org">Joe</a>
 Description: Auto generate hyperlinks for images which will links to the article.
 */


//Class define
if(!class_exists("AutoImageLinks")){
	class AutoImageLinks{
		var $adminOptionsName = 'AutoImageLinksAdminOptions';
		function autolinks($content=''){
			require_once ( dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'autolinks.php' );
			if (is_single()) {
				return $content;
			}
			$link=clean_url(get_permalink());
			$title=get_the_title();

			$autoLinkOption=get_option($this->adminOptionsName);
			$imgTitlePrefix=$autoLinkOption['Image_Title_Prefix'];
			$imgAltPrefix=$autoLinkOption['Image_Alt_Prefix'];
			$onlyFirstImg=$autoLinkOption['Only_Auto_Link_First_Image'];
				
			$autolinks=new AutoLinks($imgTitlePrefix,$imgAltPrefix,$onlyFirstImg);
			return $autolinks->handleImgLinks($content,$title,$link);
		}

		function init(){
			$this->adminOptions();
		}
		function adminOptions(){
			$adminOptions=array('Image_Title_Prefix'=>'Read Article:',
			'Image_Alt_Prefix'=>'Read Article:',
			'Only_Auto_Link_First_Image'=>'false');
			$devOptions=get_option($this->adminOptionsName);
			if(!empty($devOptions)){
				foreach($devOptions as $key=>$option){
					$adminOptions[$key]=$option;
				}
			}
			update_option($this->adminOptionsName,$adminOptions);
			return $adminOptions;
		}
		function printAdminPage(){
			$devOptions = $this->adminOptions();
			if(isset($_POST['Update_Auto_Link_Plugin_Settings'])){
				if(isset($_POST['Image_Title_Prefix'])){
					$devOptions['Image_Title_Prefix']=$_POST['Image_Title_Prefix'];
				}
				if(isset($_POST['Image_Alt_Prefix'])){
					$devOptions['Image_Alt_Prefix']=$_POST['Image_Alt_Prefix'];
				}
				if(isset($_POST['Only_Auto_Link_First_Image'])){
					$devOptions['Only_Auto_Link_First_Image']=$_POST['Only_Auto_Link_First_Image'];
				}
				update_option($this->adminOptionsName,$devOptions);
				?>
<div class="updated">
<p><strong><?php _e("Settings Updated.","autolinks");?></strong></p>
</div>
				<?php
			}//end of update
			?>
<div class="wrap">
<form method="post" action="<?php echo($_SERVER['REQUEST_URI']);?>">
<h2>AutoLinks Settings</h2>
<h3>Image title prefix</h3>
<input type="text" name="Image_Title_Prefix"
	value="<?php _e($devOptions['Image_Title_Prefix'],'autolinks')?>">
<h3>Image alt prefix</h3>
<input type="text" name="Image_Alt_Prefix"
	value="<?php _e($devOptions['Image_Alt_Prefix'],'autolinks')?>" />
<h3>Only auto link for the first image</h3>
<p><label for="Only_Auto_Link_First_Image_Yes"> <input type="radio"
	id="Only_Auto_Link_First_Image_Yes" name="Only_Auto_Link_First_Image"
	value="true"
	<?php
	if($devOptions['Only_Auto_Link_First_Image']=='true'){
		_e('checked="checked"','autolinks'); }?> />Yes</label> <label
	for="Only_Auto_Link_First_Image_No"><input type="radio"
	id="Only_Auto_Link_First_Image_No" name="Only_Auto_Link_First_Image"
	value="false"
	<?php
	if($devOptions['Only_Auto_Link_First_Image']=='false'){
		_e('checked="checked"','autolinks'); }?> />No</label></p>
<div class="submit"><input type="submit"
	name="Update_Auto_Link_Plugin_Settings"
	value="<?php _e('Update Settings', 'autolinks') ?>" /></div>
</form>

</div>
		<?php
		}//end of printAdminPage function

	}//end class define
}

//Class initialize

$autoImageLinks=NULL;

if(class_exists("AutoImageLinks")){
	$autoImageLinks=new AutoImageLinks();
}

if(!function_exists('AutoLinkAdminPanel')){
	function AutoLinkAdminPanel(){
		global $autoImageLinks;
		if(!isset($autoImageLinks)){
			return;
		}
		if(function_exists('add_options_page')){
			add_options_page('AutoLinks','AutoLinks',9,basename(__FILE__),array(&$autoImageLinks,'printAdminPage'));
		}
	}

}

//Actions and Filters
if(isset($autoImageLinks)){
	//actions
	add_action('activate_autolink/autolink.php',array(&$autoImageLinks,
'init'));
	add_action('admin_menu','AutoLinkAdminPanel');
	//filters
	add_filter('the_content', array(&$autoImageLinks,
'autolinks'));
}



?>
