<?
include('generate.php');

/**
 * Создаем страницу настроек плагина
 */
add_action('admin_menu', 'nws_yml_add_plugin_page');
function nws_yml_add_plugin_page(){
	add_options_page( 'Настройки YML для сниппетов', 'YML For Snippets', 'manage_options', 'YML_for_snippets', 'nws_yml_options_page_output' );
}

function nws_yml_options_page_output(){
	?>
	<div class="wrap">
	    	<style>
	    .form-table>tbody>tr>th {
            vertical-align:  middle;
        }
        .form-table>tbody>tr>td {
            background: #fff;
            box-shadow: inset 0 0 3px #c2c2c2;
            border-bottom: 10px solid #eee;
        }
		.fill_nws_yml_head td{
	        width:50%;
	    }
	    .fill_nws_yml_head input[type="text"]{
	        width:100%;
	    }
	    .notify{
            width: 100%;
            display: block;
            line-height: 3;
	    }
	    .loader{
	        display:none;
	    }
	    .loader img {
            width: 28px;
        }
        .fill_nws_yml_rubrics td,.fill_nws_yml_pages td{
            padding: 2px 10px;
            border-bottom: 1px solid #eee;
        }
        .fill_nws_yml_rubrics tr:last-child td,.fill_nws_yml_pages tr:last-child td{
            border-bottom:none
        }
        .yml_descr>div {
            width: 49.8%;
            display:inline-block;
            min-height: 80px;
            vertical-align: top;
            text-align: justify;
        }
        
        .yml_descr img {
            width: 100%;
            max-width:  450px;
            display:  block;
            margin-left:  auto;
        }
        .yml_descr .warning{
            color:red
        }
        .yml_descr>div>p {
            margin-bottom: 30px;
        }
        .yml_descr .kak_est{
            color: #555;
            font-size: 12px;
            border-top: 1px solid #999;
            padding-top: 10px;
        }
        .file_lista {
            font-family: monospace;
            padding:  10px;
            border:  1px solid #eee;
            font-size:  12px;
        }
        .successModal {
            display: block;
            position: fixed;
            top: 5%;
            right: 1%;
            width: 300px;
            height: auto;
            padding: 5px 20px;
            background-color: #fff;
            z-index:1002;
            overflow: auto;
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            -moz-box-shadow: 0px 1px 10px #070707;
            -webkit-box-shadow: 0px 1px 10px #070707;
            box-shadow: 0px 1px 10px #070707;
        }
        @media(max-width:1400px){
                    .yml_descr>div {
                        width: 99.8%;
                    }
                    .yml_descr img {
                        margin-right:  auto;
                    }
        }
	</style>
		<h2><?php echo get_admin_page_title() ?></h2>
<div id="saveResult"></div>
		<form action="options.php" method="POST" id="YML_for_snippets">
			<?php
				settings_fields( 'option_group' );     // скрытые защитные поля
				do_settings_sections( 'YML_for_snippets' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'nws_yml_plugin_settings');
function nws_yml_plugin_settings(){
	// параметры: $option_group, $option_name, $sanitize_callback
	register_setting( 'option_group', 'nws_yml_options', 'nws_yml_callback_santinize_iterator' );

	// параметры: $id, $title, $callback, $page
	add_settings_section( 'section_id', 'Основные настройки', '', 'YML_for_snippets' ); 

	// параметры: $id, $title, $callback, $page, $section, $args
	add_settings_field('fill_nws_yml_descr', 'Описание', 'fill_nws_yml_descr', 'YML_for_snippets', 'section_id' );
	
	add_settings_field('fill_nws_yml_head', 'Базовые настройки', 'fill_nws_yml_head', 'YML_for_snippets', 'section_id' );
	
    add_settings_field('fill_nws_yml_rubrics', 'Настройки рубрик', 'fill_nws_yml_rubrics', 'YML_for_snippets', 'section_id' );
    
    add_settings_field('fill_nws_yml_pages', 'Настройки страниц', 'fill_nws_yml_pages', 'YML_for_snippets', 'section_id' );
    
	add_settings_field('fill_nws_yml_yml', 'YML', 'fill_nws_yml_yml', 'YML_for_snippets', 'section_id' );
}

function fill_nws_yml_descr(){
        ?>
        <div class="yml_descr">
            <div>
                    <p>
                        Данный плагин предназначен для формирования сниппетов страниц и записей в поисковой системе Яндекс.
                    </p>
                    <p>
                        Плагин формирует YML файлы которые необходимо добавить в Яндекс.Вебмастер
                    </p>
                    <p class="warning">
                        <b>Внимание!</b> Для каждой рубрики генерируется свой yml файл, разбитый на несколько частей, если в рубрике больше 1000 записей.
                        <br>
                        <br>
                        В процессе генерации, при большом количестве записей страница может зависать, дождитесь окончания
                    </p>

            </div>
             <div>
                   <img src="<?=plugin_dir_url( __FILE__ ).'/images/snippet.png';?>" alt="snippet">

            </div>
             <p class="kak_est">
                        <b>Плагин разрабатывался для собственных нужд и распространяется "как есть"</b>, если вам необходим дополнительный функционал, или же любые другие услуги, пишите на почту <a href="mailto:info@new-webstudio.ru">info@new-webstudio.ru</a>
             </p>
             <p class="donate">
             <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&targets=%D0%9F%D0%BE%D0%BC%D0%BE%D1%89%D1%8C%20%D0%B2%20%D1%80%D0%B0%D0%B7%D0%B2%D0%B8%D1%82%D0%B8%D0%B8%20%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%B0&targets-hint=&default-sum=50&button-text=11&payment-type-choice=on&mobile-payment-type-choice=on&hint=&successURL=&quickpay=shop&account=410012879205197" width="100%" height="222" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
             </p>
        </div>

    <?
}


## Заполняем опцию 1
function fill_nws_yml_head(){
	$val = get_option('nws_yml_options');
	$fields = array(
	    array('name','text','Название*',20),
	    array('company','text','Компания*',200),
	    )
	?>

	<table class="fill_nws_yml_head">
	    <tbody>
	        <?foreach($fields as $field){?>
	        <tr>
	            <th><?=$field[2]?></th>
	            <td><input type="<?=$field[1]?>" name="nws_yml_options[<?=$field[0]?>]" value="<?php echo esc_attr( $val[$field[0]] ) ?>" maxlength="<?=$field[3]?>" required/></td>
	        </tr>
	        <?}?>
	        <tr>
	            <th>Доставка</th>
	            <td>
	                <input type="hidden" name="nws_yml_options[delivery]" value="0" />
	                <input type="checkbox" name="nws_yml_options[delivery]" value="1" <?=$val['delivery']==1?'checked="checked"':''?>/>
	           </td>
	        </tr>
	        <tr>
	            <th>Стоимость доставки</th>
	            <td><input type="text" name="nws_yml_options[delivery_price]" value="<?php echo esc_attr( $val['delivery_price'] ) ?>" /></td>
	        </tr>
	    </tbody>
	</table>
	<?php
}
## Заполняем опцию 2
function fill_nws_yml_rubrics(){
	$val = get_option('nws_yml_options');
	$fields = array( );
    $fields = get_terms( array(
    	'hide_empty'  => 1,  
    	'orderby'     => 'name',
    	'order'       => 'ASC',
    	'taxonomy'    => 'category',
    	'get'  => 'all'
    ) );
	?>
    
	<table class="fill_nws_yml_rubrics">
	    <thead>
	        <tr>
	            <th>Активность</th>
	            <th>Название рубрики</th>
	            <th>Стоимость</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?foreach($fields as $key => $field){?>

	        <tr>
	            <td style="width:10%;">
	                <input type="hidden" name="nws_yml_options[rubrics][<?=$field->term_id?>][active]" value="0" />
	                <input type="checkbox" name="nws_yml_options[rubrics][<?=$field->term_id?>][active]" value="1" <?=$val['rubrics'][$field->term_id]['active']==1?'checked="checked"':''?>/>
	            </td>
	            <td><?=$field->name?></td>
	            <td><input type="text" name="nws_yml_options[rubrics][<?=$field->term_id?>][price]" value="<?php echo esc_attr( empty($val['rubrics'][$field->term_id]['price'])?"":$val['rubrics'][$field->term_id]['price'] ) ?>" /></td>
	        </tr>
	        <?}?>
	    </tbody>
	</table>
	<?php
}
## Заполняем опцию 2.5
function fill_nws_yml_pages(){
	$val = get_option('nws_yml_options');
	$fields = array( );
    $fields = get_pages( array(
        'sort_order'   => 'ASC',
        'sort_column'  => 'post_title',
        'hierarchical' => 1,
        'exclude'      => '',
        'include'      => '',
        'meta_key'     => '',
        'meta_value'   => '',
        'authors'      => '',
        'child_of'     => 0,
        'parent'       => -1,
        'exclude_tree' => '',
        'number'       => '',
        'offset'       => 0,
        'post_type'    => 'page',
        'post_status'  => 'publish',
    ));
	?>
    
	<table class="fill_nws_yml_pages">
	    <thead>
	        <tr>
	            <th>Активность</th>
	            <th>Название страницы</th>
	            <th>Стоимость</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?foreach($fields as $key => $field){?>
    
	        <tr>
	            <td style="width:10%;">
	                <input type="hidden" name="nws_yml_options[pages][<?=$field->ID?>][active]" value="0" />
	                <input type="checkbox" name="nws_yml_options[pages][<?=$field->ID?>][active]" value="1" <?=$val['pages'][$field->ID]['active']==1?'checked="checked"':''?>/>
	            </td>
	            <td><a href="<?=get_permalink($field->ID)?>"><?=$field->post_title?></a></td>
	            <td><input type="text" name="nws_yml_options[pages][<?=$field->ID?>][price]" value="<?php echo esc_attr( empty($val['pages'][$field->ID]['price'])?"":$val['pages'][$field->ID]['price'] ) ?>" /></td>
	        </tr>
	        <?}?>
	    </tbody>
	</table>
	<?php
}
## Заполняем опцию 3
function fill_nws_yml_yml(){
	    $val = get_option('nws_yml_options');
        $globalpath = wp_normalize_path(ABSPATH);
        $ansver = '';
        $paths = array();
        $url = get_site_url();
        
        
        $scanned_directory = array_diff(scandir($globalpath), array('..', '.'));
        foreach($scanned_directory as $file){
            if(preg_match("/^yandex_market-.*\.yml$/mi", $file)){
                $paths[] = array('path'=>$globalpath.$file,'name'=>$file);
            }
        }
        if(!empty($paths)){
            foreach($paths as $path){
                $ansver .= "В последний раз файл ".$path['name']." был изменен: " . date ("d/m/Y H:i:s.", filemtime($path['path']))."<br>";
            }
        }
	?>
        <div class="notify update_file">
           <?=$ansver?>
        </div>
         <?if(!empty($ansver)){?>
           <br><br><p>Список ссылок которые необходимо вставить в Яндекс.Вебмастер</p>
         <?}?>
           <p class="file_lista">
               <?foreach($paths as $path){echo $url.'/'.$path['name'].'<br>';}?>
           </p>
           <br>

        <button class="button button-primary generate_yml">Генерировать YML</button>
        <span class="loader"><img src="data:image/gif;base64,R0lGODlhMgAyAPUZADy97UfB7i+68YnO6I3Q6Rax7xCv7y247F3G7YLL5gCu72LO9ii48JfU6nzI5Te/8qnX56/Z6aPU5lDG85DR6a3Y6KHS5US76XbU9qXV5hu08gCw8jK67InZ+JXd+D205YbN52K933LE41a43J7g+afk+xGy8rXZ5qnT46rN25nO4rvc6JPM4Z3Q48Hc5rDW5W272mzC4aTS5A6x8tPs9gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/wtYTVAgRGF0YVhNUDw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjQ3RjlCOEY4NDMxMTFFNTlEMThEMEJDQ0ZCRDdCNkUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjQ3RjlCOTA4NDMxMTFFNTlEMThEMEJDQ0ZCRDdCNkUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDMjA1QTExMDg0MkYxMUU1OUQxOEQwQkNDRkJEN0I2RSIg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"></span>
        <div class="notify">
           
        </div>
	<?php
	add_action('admin_print_footer_scripts', 'nws_yml_generate_javascript', 99);
    function nws_yml_generate_javascript() {
    	?>
    	<script>
    	jQuery(document).ready(function($) {
    	    function runCreatingYmlRubric(head,rubrics,pages,i){
            if(i<rubrics.nws_yml_options.rubrics.length){
                var rubric = {
                    nws_yml_options:{
                        rubrics:[]
                    }
                }
                rubric.nws_yml_options.rubrics[i] = rubrics.nws_yml_options.rubrics[i]
    	        var data = {
        			action: 'nws_yml_generate',
        			inputdata: {
        			'head':head,
        			'rubrics':rubric,
        			'pages':pages,
        			}
        		};
            	$.ajax({
                           url: ajaxurl,
                           data: data,
                           async: false,
                           type: 'POST'
                    })
                    .done(function(html){
                        if(html.length > 2){
                            var json = $.parseJSON(html);
                            $('.update_file').append(json.text);
                            $('.file_lista').append(json.link);
                        }
                        setTimeout(runCreatingYmlRubric(head,rubrics,pages,i+1),3000)
                    })
                }else{
                    $('.loader').hide(); 

                }
    	    }
    	    function runCreatingYmlPages(head,rubrics,pages,i){
                if(pages.nws_yml_options.pages.length>0){
                var rubric = []
    	        var data = {
        			action: 'nws_yml_generate',
        			inputdata: {
        			'head':head,
        			'rubrics':rubric,
        			'pages':pages,
        			}
        		};
            	$.ajax({
                           url: ajaxurl,
                           data: data,
                           async: false,
                           type: 'POST'
                    })
                    .done(function(html){
                        if(html !== ""){
                            var json = $.parseJSON(html);
                            $('.update_file').append(json.text);
                            $('.file_lista').append(json.link);
                        }
                    })
                }
    	    }
    	    $('.generate_yml').click(function(e){
    	      e.preventDefault();
    	      

                    jQuery('#YML_for_snippets').ajaxSubmit({
                    success: function(){
                    jQuery('#saveResult').html("<div id='saveMessage' class='successModal'></div>");
                    jQuery('#saveMessage').append("<p>Настройки успешно сохранены</p>").show();
                    }, 
                    timeout: 5000
                    }); 
                    setTimeout("jQuery('#saveMessage').hide('slow');", 5000);


                
                
                var head = $('.fill_nws_yml_head input[name*="nws_yml_options"]').serializeObject()
                var rubrics = $('.fill_nws_yml_rubrics input[name*="nws_yml_options"]').serializeObject()
                var pages = $('.fill_nws_yml_pages input[name*="nws_yml_options"]').serializeObject()
        	    if(head.nws_yml_options.name.length>0&&head.nws_yml_options.name.length<20&&head.nws_yml_options.company.length>0){
                $('.loader').show();
                $('.update_file').html('');
                $('.file_lista').html('');
                
                
                    $.ajax({
                           url: ajaxurl,
                           data: {
                    			action: 'nws_yml_generate',
                    			inputdata: {'removefiles':true}
                    		},
                           async: false,
                           type: 'POST'
                    })
                    

                    
                    runCreatingYmlRubric(head,rubrics,[], 1) 
                    runCreatingYmlPages(head,[],pages, 1)
                    
        	    } else {
        	        alert('Заполните все поля!')
        	    }
    	    })
    	});
    	</script>
    	<?php
    }
}
