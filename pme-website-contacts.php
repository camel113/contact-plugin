<?php
/* Plugin Name: PME Website contacts
*/
add_action('wp_enqueue_scripts', 'pmec_add_styles');
function pmec_add_styles() {
  wp_register_style('pme-website-contacts_css', plugins_url('css/style.css', __FILE__));
  wp_enqueue_style('pme-website-contacts_css');
}

add_action('admin_init','pmec_plugin_init');
function pmec_plugin_init()
{
    wp_register_style('pme-website-contacts_css', plugins_url('css/admin.css', __FILE__));
    wp_enqueue_style('pme-website-contacts_css');
    wp_enqueue_script('jquery');
    wp_register_script('pme-website-contacts_common', plugins_url('js/pme-website-contacts_common.js', __FILE__),array("jquery"));
    wp_enqueue_script('pme-website-contacts_common');
}

add_shortcode("pme-website-contacts", "pmec_shortcode_function");

function pmec_shortcode_function($atts,$content) {

    extract(shortcode_atts(array(

               'id' => ''), $atts));

    $contacts_infos = get_post_meta($id, "_pmec_pme_website_contacts", true);
    $contacts_infos = ($contacts_infos != '') ? $contacts_infos : array();

    $content = '<dl id="abg-contacts">';
    $content .= '<dd>Nom</dd>
                 <dt class="abg-adresse abg-adr-nom">'.$contacts_infos[0].'</dt>';
    $content .= '<dd>Adresse ligne 1</dd>
                 <dt class="abg-adresse abg-adr-ligne1" title="Adresse">'.$contacts_infos[1].'</dt>';
    if($contacts_infos[2] != ''){
      $content .= '<dd>Adresse ligne 2</dd>
                   <dt class="abg-adresse abg-adr-ligne2" title="Adresse">'.$contacts_infos[2].'</dt>';  
    }                                             
    $content .='<dd>Ville</dd>
                <dt class="abg-adresse abg-adr-ville" title="Ville">'.$contacts_infos[3].'<dt>';   
    if($contacts_infos[4] != ''){
      $content .= '<dd>E-mail<dd>
                   <dt class="abg-mail" title="E-mail">'.$contacts_infos[4].'</dt>';  
    }
    if($contacts_infos[5] != ''){
      $content .= '<dd>Téléphone<dd>
                   <dt class="abg-phone" title="Téléphone">'.$contacts_infos[5].'</dt>';  
    }
    if($contacts_infos[6] != ''){
      $content .= '<dd>Mobile<dd>
                   <dt class="abg-mobile" title="Mobile">'.$contacts_infos[6].'</dt>';  
    }
    if($contacts_infos[7] != ''){
      $content .= '<dd>Mobile<dd>
                   <dt class="abg-fax" title="Fax">'.$contacts_infos[7].'</dt>';  
    }  
    $content .= '</dl>';                     
    return    $content;
}
add_action('init', 'pmec_register_pme_website_contacts');

function pmec_register_pme_website_contacts() {

    $labels = array(

       'menu_name' => 'PME Website contacts',
       'add_new_item' => 'Add new contacts',
       'singular_name' => 'PME Website contacts',
       'name' => 'PME Website contacts'

    );

    $args = array(

       'labels' => $labels,

       'hierarchical' => true,

       'description' => 'Slideshows',

       'supports' => 'title',

       'public' => true,

       'show_ui' => true,

       'show_in_menu' => true,

       'show_in_nav_menus' => true,

       'publicly_queryable' => true,

       'exclude_from_search' => false,

       'has_archive' => true,

       'query_var' => true,

       'can_export' => true,

       'rewrite' => true,

       'capability_type' => 'post'

    );

    register_post_type('pme_website_contacts', $args);

}

add_action('add_meta_boxes', 'pmec_plugin_meta_box');

function pmec_plugin_meta_box() {

    add_meta_box("pmec-pme-website-contacts-metabox", "PME Website contacts", 'pmec_view_metabox', "pme_website_contacts", "normal");

}

function pmec_view_metabox() {
    global $post;

    $contacts_values = get_post_meta($post->ID, "_pmec_pme_website_contacts", true);
    // print_r($gallery_images);exit;
    $contacts_values = ($contacts_values != '') ? $contacts_values : array();

    // Use nonce for verification
    $html =  '<input type="hidden" name="pmec_box_nonce" value="'. wp_create_nonce(basename(__FILE__)). '" />';
    $html .= '
    <div pmec-form>
      <div>
        <label for="Upload contacts">Nom</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[0].'" />
      </div>
      <div>  
        <label for="Upload Images">Adresse ligne 1</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[1].'" />
      </div>
        <div>
        <label for="Upload contacts">Adresse ligne 2</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[2].'" />
      </div>
      <div>  
        <label for="Upload Images">Ville</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[3].'" />
      </div>
      <div>
        <label for="Upload contacts">Mail</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[4].'" />
      </div>
      <div>  
        <label for="Upload Images">Téléphone fixe</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[5].'" />
      </div>
      <div>
        <label for="Upload contacts">Mobile</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[6].'" />
      </div>
      <div>  
        <label for="Upload Images">Fax</label>
        <input id="pmec_slider_upload" type="text" name="contacts_values[]" value="'.$contacts_values[7].'" />
      </div>
    </div>
      ';
    echo $html;
}
add_action('save_post', 'pmec_save_contacts');

function pmec_save_contacts($post_id) {

    // verify nonce

    if (!wp_verify_nonce($_POST['pmec_box_nonce'], basename(__FILE__))) {

       return $post_id;

    }

    // check autosave

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

       return $post_id;

    }

    // check permissions

    if ('pme_website_contacts' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {

       /* Save Slider Images */


       //print_r($_POST['gallery_img']);exit;

       $contacts_values= (isset($_POST['contacts_values']) ? $_POST['contacts_values'] : '');

       //print_r($opening_times);exit;

       update_post_meta($post_id, "_pmec_pme_website_contacts", $contacts_values);

       return $post_id;

    } else {

       return $post_id;

    }
}
/* Define shortcode column in Rhino Slider List View */
add_filter('manage_edit-pme_website_contacts_columns', 'pmec_set_custom_edit_pme_website_contacts_columns');
add_action('manage_pme_website_contacts_posts_custom_column', 'pmec_custom_pme_website_contacts_columns', 10, 2);

function pmec_set_custom_edit_pme_website_contacts_columns($columns) {
    return $columns
            + array('schedule_shortcode' => __('Shortcode'));
}

function pmec_custom_pme_website_contacts_columns($column, $post_id) {

    $schedule_meta = get_post_meta($post_id, "_fwds_plugin_meta", true);
    $schedule_meta = ($schedule_meta != '') ? $schedule_meta : array();

    switch ($column) {
        case 'schedule_shortcode':
            echo "[pme-website-contacts id='$post_id' /]";
            break;

    }
}