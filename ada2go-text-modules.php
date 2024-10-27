<?php
/*
Plugin Name: Ada2go - Text Modules
Description: ada2go - Text Modules outputs a clickable form in the frontend to output predefined text. You can use it, for example, for participant feedback, job references or similar.
Version: 1.8
Author: Heiko von ada2go.de
Author URI: https://ada2go.de/
Text Domain: ada2go-text-modules
*/

defined( 'ABSPATH' ) or die( 'Huuuuuuh?' );

 //======================================================================
//======================================================================
//
// FOLLOWING ARE FUNCTIONS FOR BASE WORDPRESS HANDLING
// LIKE ACTIVATION, MENU ...
//
//======================================================================
 //====================================================================== 

//-----------------------------------------------------
// do     = create the shortcode for the form in frontend
//-----------------------------------------------------
function a2g_tm( $atts ) {
global $wpdb;
$a = shortcode_atts( array(
                    'cat' => ''
                    ), $atts );

if($_GET["action"]=="result") { 
  include "a2g_tm_result.php"; 
} 
  else {
  if(!empty($a['cat'])) {
  
  $sql_query = "SELECT *  FROM ".$wpdb->prefix."a2g_tm_kategorie WHERE FIND_IN_SET(`catID`, '".$a['cat']."') ORDER BY catSort";
  $a2g_tm_built_return .= "<form action=\"" . get_permalink() . "?action=result\" method=\"post\" target=\"_blank\">";
  $a2g_tm_get_categorys	=	$wpdb->get_results($sql_query);
  }
      
      foreach ( $a2g_tm_get_categorys as $a2g_tm_get_category ) {
      
      $a2g_tm_built_return .= "<h2>" . $a2g_tm_get_category->catName . "</h2><small>". html_entity_decode( $a2g_tm_get_category->catDesc )."</small><br><br>";
      
      $a2g_tm_get_texts	=	$wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "a2g_tm_text WHERE catID = " . $a2g_tm_get_category->catID . "");
      
      $count = 0;
          
          foreach ( $a2g_tm_get_texts as $a2g_tm_get_text ) {
          
              if(!empty($a2g_tm_get_text->textID)) {
              $count=md5(rand());
              $a2g_tm_built_return .= '<input type="checkbox" name="' 
              . $count . '" value="' . $a2g_tm_get_category->catID . ',' . $a2g_tm_get_text->textID . '">' . $a2g_tm_get_text->text_ueberschrift;
              }
          
          }
      $a2g_tm_built_return .= "<hr>";
    }
  $a2g_tm_built_return .= "<br><input type=\"submit\" value=\"".__( 'Abschicken!', 'ada2go-text-modules' )."\"></form>";
  }
 return $a2g_tm_built_return;
}

//-----------------------------------------------------
// do     = add a option if is not exist to safe simple css code. best solution ;-P
//-----------------------------------------------------
if (FALSE === get_option('a2g_tm_output_css') && FALSE === update_option('a2g_tm_output_css',FALSE)) {
  add_option('a2g_tm_output_css','.a2g-out-html {
              border:1px solid #000;
              }
              .a2g-out-body {
              background-color:black; color:white;
              }');
}

//-----------------------------------------------------
// do     = add submenu to wordpress backend
//-----------------------------------------------------
function a2g_tm_options_submenu() {
    add_submenu_page(
                      'options-general.php',
                      'Text Modules',
                      'Text Modules',
                      'administrator',
                      'a2g_tm_settings',
                      'a2g_tm_settings_page' );
}
    
//-----------------------------------------------------
// do     = function for the register_activation_hook
//        = create database tables
//-----------------------------------------------------
function a2g_tm_activate() {
global $wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

      $charset = $wpdb->get_charset_collate();
      $charset_collate = $wpdb->get_charset_collate();
              
              // check if tables allready exist
              $a2g_tm_check_exist_kategorie_table_while_install_update	=	$wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."a2g_tm_kategorie");
              $a2g_tm_check_exist_texte_table_while_install_update	=	$wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."a2g_tm_text");
              
              // when not, create a $sql part 1
              if(! $a2g_tm_check_exist_kategorie_table_while_install_update ) {
                   $sql_kategorie = "CREATE TABLE ".$wpdb->prefix."a2g_tm_kategorie ( 
                          `catID` INT(11) NOT NULL AUTO_INCREMENT,
                          `catName` VARCHAR(255) NOT NULL,
                          `catDesc` TEXT NOT NULL , 
                          `catSort` TEXT NOT NULL , 
                          PRIMARY KEY (`catID`)
                          ) $charset_collate; ";
              }
              // when not, create a $sql part 2
              if(! $a2g_tm_check_exist_texte_table_while_install_update ) {
                   $sql_text = "CREATE TABLE ".$wpdb->prefix."a2g_tm_text ( 
                          `textID` INT(11) NOT NULL AUTO_INCREMENT ,
                          `catID` INT(11) NOT NULL ,
                          `text_ueberschrift` TEXT NOT NULL ,
                          `text` TEXT NOT NULL ,
                          PRIMARY KEY (`textID`)
                          ) $charset_collate;";
              }
          // check if $sql_text is set -> true = create
          if(isset($sql_text)) {
          dbDelta( $sql_kategorie.$sql_text );
          } 
}

//-----------------------------------------------------
// do     = include the require setting file
//----------------------------------------------------- 
function a2g_tm_settings_page() { 
  require "settings_page.php";
}

//-----------------------------------------------------
// do     = load individual CSS File
//----------------------------------------------------- 
function a2g_tm_css() {
  wp_enqueue_style( 'ada2go-text-modules', plugin_dir_url( __FILE__ ) . 'ada2go-text-modules.css' );  
}

//-----------------------------------------------------
// do     = add actions, shortcode, other
//-----------------------------------------------------
add_shortcode( 'a2gtm', 'a2g_tm' );
add_action( 'wp_enqueue_scripts', 'a2g_tm_css' );  
add_action( 'admin_enqueue_scripts', 'a2g_tm_css');
add_action("admin_menu", "a2g_tm_options_submenu");
register_activation_hook( __FILE__, 'a2g_tm_activate' );

 //======================================================================
//======================================================================
//
// FOLLOWING ARE FUNCTIONS TO GET DATA BY ID OR DATA GENERALLY
//
//======================================================================
 //======================================================================

//-----------------------------------------------------
// return     = get the number of safed categorys in database
// why        = using for while counting in settings_page.php
//-----------------------------------------------------
function a2g_tm_get_highest_cat_sort() {
global $wpdb;

  $a2g_tm_get_highest_cat	=	$wpdb->get_var("SELECT MAX(catSort) FROM " . $wpdb->prefix . "a2g_tm_kategorie");
  
 return $a2g_tm_get_highest_cat;
}

//-----------------------------------------------------
// att        = $a2g_tm_cat is the given category id
// return     = get the number of safed categorys in database
// why        = using for while counting in settings_page.php
//-----------------------------------------------------
function a2g_tm_get_catsort_by_catid( $a2g_tm_cat ) {
global $wpdb;

  $a2g_the_cat = $wpdb->get_var("SELECT catSort FROM " . $wpdb->prefix . "a2g_tm_kategorie WHERE catID = $a2g_tm_cat");

 return $a2g_the_cat;
}

//-----------------------------------------------------
// att        = $a2g_tm_cat is the given category id
// return     = select the description of a category id
// why        = using for output in table and textarea in settings_page.php
//-----------------------------------------------------
function a2g_tm_edit_cat_by_catid( $a2g_tm_cat ) {
global $wpdb;

  $a2g_the_cat = $wpdb->get_var("SELECT catDesc  FROM " . $wpdb->prefix . "a2g_tm_kategorie WHERE catID = $a2g_tm_cat");

 return html_entity_decode( $a2g_the_cat );
}

//-----------------------------------------------------
// att        = $a2g_tm_text is the given text id
// return     = select the text of a text id
// why        = using for output in table and textarea in settings_page.php
//-----------------------------------------------------
function a2g_tm_get_text_by_textid( $a2g_tm_text ) {
global $wpdb;

  $a2g_the_text = $wpdb->get_var("SELECT text  FROM " . $wpdb->prefix . "a2g_tm_text WHERE textID = $a2g_tm_text");

 return html_entity_decode( $a2g_the_text );
}

//-----------------------------------------------------
// att        = $a2g_tm_cat is the given category id
// return     = select the category name of a category id
// why        = using for output in table, ids and textarea in settings_page.php
//-----------------------------------------------------
function a2g_tm_get_cat_by_catid( $a2g_tm_cat ) {
global $wpdb;

  $a2g_the_cat = $wpdb->get_var("SELECT catName  FROM " . $wpdb->prefix . "a2g_tm_kategorie WHERE catID = $a2g_tm_cat");

 return html_entity_decode( $a2g_the_cat );
}


 //======================================================================
//======================================================================
//
// FOLLOWING ARE FUNCTIONS TO CREATE <OPTION> LISTS, TABLES OR DROPDOWNS
//
//======================================================================
 //======================================================================

//-----------------------------------------------------
// return     = generate the html <option> list for sorting dropdown
// why        = using for dropdown in settings_page.php
//-----------------------------------------------------
function a2g_tm_get_select_options_for_catsort() {
global $wpdb;

  $a2g_tm_db_get_cat_sorts	=	$wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "a2g_tm_kategorie");
  
    foreach ( $a2g_tm_db_get_cat_sorts as $a2g_tm_db_get_cat_sort ) {
      $a2g_tm_return_option_sort .= '<option value="'.$a2g_tm_db_get_cat_sort->catSort.'">'.$a2g_tm_db_get_cat_sort->catSort .'</option>';
    }
    
 return $a2g_tm_return_option_sort;
}

//-----------------------------------------------------
// return     = generate the html table list for showing categorys
//----------------------------------------------------- 
function a2g_tm_while_categorys() {
global $wpdb;

$a2g_tm_get_categorys	=	$wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."a2g_tm_kategorie ORDER BY catSort");

  foreach ( $a2g_tm_get_categorys as $a2g_tm_get_category ) {
    $a2g_built_category_table .= '<tr>
                                  <td>' . $a2g_tm_get_category->catID . '</td>
                                  <td>' . $a2g_tm_get_category->catName . '
                                  <br>
                                  <a 
                                  onclick="return confirm(\''
                                  .__('Kategorie wirklich l&ouml;schen?', 'ada2go-text-modules' ) . '\')" 
                                  href="options-general.php?page=a2g_tm_settings&action=delcat&id=' . $a2g_tm_get_category->catID . '">' 
                                  . __('l&ouml;schen', 'ada2go-text-modules' ) . '</a> | <a 
                                  href="options-general.php?page=a2g_tm_settings&action=editkat&id=' 
                                  . $a2g_tm_get_category->catID . '">' 
                                  . __('bearbeiten', 'ada2go-text-modules' ) .'</a></td>
                                  <td>' . wp_trim_words( $a2g_tm_get_category->catDesc, '100', ' ...' ) . '</td>
                                  <td>' . $a2g_tm_get_category->catSort . '</td></tr>
                                  ';
    }
 return $a2g_built_category_table;
}

//-----------------------------------------------------
// return     = generate the html table list for showing textes
//----------------------------------------------------- 
function a2g_tm_while_textes() {
global $wpdb;

$a2g_tm_get_textes	=	$wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "a2g_tm_text");
  foreach ( $a2g_tm_get_textes as $a2g_tm_get_text ) {
    $a2g_built_text_table .= '<tr>
                              <td>' . $a2g_tm_get_text->textID . '</td>
                              <td>' . wp_trim_words( $a2g_tm_get_text->text, '100', ' ...' ) . '
                              <br><a onclick="return confirm(\'' 
                              . __('Text wirklich l&ouml;schen?', 'ada2go-text-modules' ) . '\')" 
                              href="options-general.php?page=a2g_tm_settings&action=deltext&id='.$a2g_tm_get_text->textID.'">'
                              .__('l&ouml;schen', 'ada2go-text-modules' ).'</a> | <a 
                              href="options-general.php?page=a2g_tm_settings&action=edittext&id='.$a2g_tm_get_text->textID.'">'
                              . __('bearbeiten', 'ada2go-text-modules' ) . '</a></td>
                              <td>' . a2g_tm_get_cat_by_catid($a2g_tm_get_text->catID) . '</td></tr>';
  }

 return $a2g_built_text_table;
}

//-----------------------------------------------------
// return     = generate the text output for the document table
//----------------------------------------------------- 
function a2g_tm_while_safed_textes() {
global $wpdb;

$directory = ABSPATH . 'wp-content/plugins/ada2go-text-modules/reslut-safes/';
$files = glob($directory . '*.{html}', GLOB_BRACE);

  foreach($files as $file)
  {
    $path = parse_url($file, PHP_URL_PATH);
    $path = basename($path);
    echo "<tr><td>Pfad: " . $file . " <br><a href='../wp-content/plugins/ada2go-text-modules/reslut-safes/". $path . "' target='_blank'>Datei &ouml;ffnen</a> | 
    <a href=\"options-general.php?page=a2g_tm_settings&action=deldocument&path=" .$file. "\">Datei l&ouml;schen</a></td></tr>";
  }

 return;
}

 //======================================================================
//======================================================================
//
// FOLLOWING ARE FUNCTIONS WICH ALREADY EXIST AND HAVE BEEN CREATED TWICE
// THESE FUNCTIONS WILL BE REMOVED IN LATER UPDATES - DONT USE IT
//
//======================================================================
 //======================================================================
 
 //-----------------------------------------------------
// att        = $a2g_tm_text is the given text id
// return     = select the text of a text id
// why        = using for output in table, ids and textarea in settings_page.php
//-----------------------------------------------------
function a2g_tm_edit_text_by_textid( $a2g_tm_text ) {
global $wpdb;

  $a2g_the_text = $wpdb->get_var("SELECT text  FROM " . $wpdb->prefix . "a2g_tm_text WHERE textID = $a2g_tm_text");
  
 return html_entity_decode( $a2g_the_text );
}