<?php
/* 
Plugin Name: WP Synonym Plugin
Plugin URI: http://wordpress.org/extend/plugins/wp-synonym-plugin/
Description: <strong><em>Instructions:</em></strong> Find synonymous words automatically instead of gruelingly reaching for the thesaurus. While you edit your posts/pages/titles mark the word of which you want to view a list of synonyms, hit SHIFT key and simply select the word you'd like to replace it with.
Version: 1.0 
Author: Nathan Baker
Author URI: 	
*/

/*  Copyright 2010 Nathan Baker
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function install_wp_synonym_plugin_js_file() {
	?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
	<script type="text/javascript" src="/tab.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
	jQuery(document).ready(function () {

     $("textarea").tabby();

});
		function mySynonymSetCookie(c_name,value,expiredays){
			var exdate = new Date();
			exdate.setDate(exdate.getDate()+expiredays);
			document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
		}

		function mySynonymGetCookie(c_name){
			if(document.cookie.length>0){
				c_start=document.cookie.indexOf(c_name + "=");
				if(c_start!=-1){
					c_start=c_start + c_name.length+1;
					c_end=document.cookie.indexOf(";",c_start);
					if(c_end==-1) c_end=document.cookie.length;
					return unescape(document.cookie.substring(c_start,c_end));
				}
			}
			return "";
		}

		function get_list_from_xml_response(xml){ 
			if(xml.length==0) return false;
			
			if($(xml).find('w').length==0) return false;

			response = '';
			
			response+= '<style type="text/css">';
			response+= '.response-word-list-container{ font-family: Aria, Helvetica, sans-serif; font-size: 12px; margin: 15px 0px 0px 15px; padding: 0; border: 1px solid #bbb; background-color: #fbfbfb; -moz-border-radius: 7px; -khtml-border-radius: 7px; -webkit-border-radius: 7px; border-radius: 7px; position: fixed; z-index: 9999; top:'+mySynonymGetCookie('y_axis')+'px; left:'+mySynonymGetCookie('x_axis')+'px; top: 0; left: 50%; } ';
			response+= '.response-word-list { margin: 0px 5px; padding: 0; max-height: 300px; overflow: auto; } ';
			response+= '.response-word { margin:0; padding: 0; list-style: none; } ';
			response+= '.synonymous-word { color: #21759b; text-decoration: none; } ';
			response+= '.synonymous-word:hover { border-bottom: 1px dotted #21759b; } ';
			response+= '.response-word-list-container p { padding: 0; margin: 5px; color: #7c7c7c; font-size: 13px; font-style: italic; font-family: Georgia, Garamond, serif; text-align: center; } ';
			response+= '.synoynm-word-decoration { font-size: 30px; } ';
			response+= '.synonym-list-copyright { font-size: 9px; vertical-align: top; } ';
			response+= '</style>';
			
			response+= '<div class="response-word-list-container"><p class="synonym-list-title">WP Synonym Plugin</p><ul class="response-word-list">';
			
			$(xml).find('w').each(function(){
				if($(this).attr('r')!='ant')
					response+= '<li class="response-word"><a title="synonym" class="synonymous-word" href="#">'+$(this).text()+'</a></li>';
			});
			response+= '</ul><p class="synoynm-word-decoration">&#9830;</p></div>';
			$('body').append(response);
			
			$('.synonymous-word').click(function(){
				var synonym = $(this).text();
				
				var txt_field = mySynonymGetCookie('txt_field');
				var upToWord = $.trim(mySynonymGetCookie('upToWord'));
				var fromWord = $.trim(mySynonymGetCookie('fromWord'));

				var new_position = $('#'+txt_field).offset();
				
				$('#'+txt_field).val(upToWord+' '+synonym+' '+fromWord);
				
				$('.response-word-list-container').remove();
			
				return false;
				
			});
			
			$(document).click(function(){
				$('.response-word-list-container').remove();
			});
			
			return true;
		}
		
		function get_synonyms(text){ 
			var url = '<?php echo get_bloginfo('wpurl').'/'. PLUGINDIR .'/' . dirname( plugin_basename(__FILE__)); ?>/xml_response.php?word='+text;
			$.get(url,{  } , get_list_from_xml_response);
		} 

		$(':text, textarea').keydown(function(event){
			if (event.shiftKey) {
			
				/* remove any open synoynm boxes */
				$('.response-word-list-container').remove();
			
				txt_field = $(this).attr('id'); /* for later */
				var text = '';
				if ('selectionStart' in this){
					length = this.selectionEnd - this.selectionStart;

					upToWord = $(this).val().substr(0,this.selectionStart);
					fromWord = $(this).val().substr(length+(upToWord.length));
					
					mySynonymSetCookie('upToWord',upToWord,360000);
					mySynonymSetCookie('fromWord',fromWord,360000);
					mySynonymSetCookie('txt_field',txt_field,360000);

					text = $.trim($(this).val().substr(this.selectionStart, length));
				} else if (document.selection) {
					text = $.trim(document.selection.createRange().text);
				}
				if (text.length) {
				
					/* get mouse coordinates */
					$(document).mousemove(function(e){
						mySynonymSetCookie('x_axis',e.pageX,360000);
						mySynonymSetCookie('y_axis',e.pageY,360000);
					});
				
					get_synonyms(text);
				}
			}
		});
	});
	</script>
	<?php
}
add_action('admin_head','install_wp_synonym_plugin_js_file',99);

