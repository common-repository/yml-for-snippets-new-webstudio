<?
include('YandexMarket.php');
add_action( 'wp_ajax_nws_yml_generate', 'nws_yml_generate_callback' );

//итерационный обход многомерного массива для проверки всех полей
function nws_yml_callback_santinize(&$item, $key) {
        $item = sanitize_text_field($item);
}

function nws_yml_callback_santinize_iterator($array) {
        array_walk_recursive($array, 'nws_yml_callback_santinize');
        return $array;
}
//логгер для тестрирования
function nws_yml_callback_log_write($data_array){
   $file = wp_normalize_path( ABSPATH ) . "nws_log.log";
   $log = PHP_EOL.'['.date("Y-m-d H:i:s").']'.PHP_EOL.var_export( $data_array, true );
   file_put_contents( $file, $log, FILE_APPEND );
}

//---------



function nws_yml_generate_callback() {
    $inputdata = ( $_POST['inputdata'] );
    
    $inputdata = nws_yml_callback_santinize_iterator($inputdata);
    
    if(isset($inputdata['removefiles'])&&$inputdata['removefiles']){
        $mask = wp_normalize_path( ABSPATH ) . "yandex_market-*.yml";
        array_map( "unlink", glob( $mask ) );   
    }
    
    
    if(empty($inputdata['head']['nws_yml_options']['delivery_price'])){
        $inputdata['head']['nws_yml_options']['delivery_price'] = 0;
    }
    $paths = array();
    $maxposts=1000;
    if(!empty($inputdata['rubrics']['nws_yml_options']['rubrics'])){
    foreach($inputdata['rubrics']['nws_yml_options']['rubrics'] as $key=>$rubric){
    if(isset($rubric['active'])){
        if($rubric['active']){
                $filename = 'yandex_market-';
                $filenameend='.yml';
                $path = wp_normalize_path( ABSPATH ) . $filename.$key.$filenameend;
                $xml = new Nws_Yml_YandexMarket;

                
                $args = array(
                    'posts_per_page' => -1,
                    'cat' => $key,
                    'post_type' => 'post'
                );
                
                $query = new WP_Query;
                $my_posts = $query->query($args);
                
                
                if($query->post_count<$maxposts){
                    $xml->configGenerate($inputdata['head']['nws_yml_options']['name'],get_site_url(),$inputdata['head']['nws_yml_options']['company'],$inputdata['head']['nws_yml_options']['delivery'],$inputdata['head']['nws_yml_options']['delivery_price']);
                    $xml->createYML();
                    foreach( $my_posts as $pst ){
                    $params = array(
                        'url' => get_permalink($pst->ID),
                        'price' => $rubric['price'],
                        'picture' => get_the_post_thumbnail_url( $pst->ID,'full'),
                        'delivery' => $inputdata['head']['nws_yml_options']['delivery'],
                        'local_delivery_cost' => $inputdata['head']['nws_yml_options']['delivery_price'],
                        'name' => $pst->post_title,
                        'description' => wp_trim_words(strip_tags($pst->post_content),15,'...'),
                    );
                    $xml->addOffer($params,$pst->ID);
                    }
                    
                    $xml->saveYML($path); 
                    $paths[]=array('path'=>$path,'name'=>$filename.$key.$filenameend); 
                } else {
                        $subfiles = ceil($query->post_count/$maxposts);
                        for ($i = 0; $i < $subfiles; $i++) {
                            $xml->configGenerate($inputdata['head']['nws_yml_options']['name'],get_site_url(),$inputdata['head']['nws_yml_options']['company'],$inputdata['head']['nws_yml_options']['delivery'],$inputdata['head']['nws_yml_options']['delivery_price']);
                            $xml->createYML();
                            $path = wp_normalize_path( ABSPATH ) . $filename.$key.'_'.$i.$filenameend;
                            $countposts = 0;
                            foreach( $my_posts as $pst ){
                                if($countposts>($i)*$maxposts&&$countposts<=($i+1)*$maxposts){
                                    $params = array(
                                        'url' => get_permalink($pst->ID),
                                        'price' => $rubric['price'],
                                        'picture' => get_the_post_thumbnail_url( $pst->ID,'full'),
                                        'delivery' => $inputdata['head']['nws_yml_options']['delivery'],
                                        'local_delivery_cost' => $inputdata['head']['nws_yml_options']['delivery_price'],
                                        'name' => $pst->post_title,
                                        'description' => wp_trim_words(strip_tags($pst->post_content),15,'...'),
                                    );
                                    $xml->addOffer($params,$pst->ID);
                                }
                                $countposts++;
                            }
                            $xml->saveYML($path); 
                            $paths[]=array('path'=>$path,'name'=>$filename.$key.'_'.$i.$filenameend); 
                        }
                            

                }
            }
    }
    }
    }
    
    if(!empty($inputdata['pages']['nws_yml_options']['pages'])){
    $filename = 'yandex_market-pages.yml';
    $path = wp_normalize_path( ABSPATH ) . $filename;
    $xml = new Nws_Yml_YandexMarket;
    $xml->configGenerate($inputdata['head']['nws_yml_options']['name'],get_site_url(),$inputdata['head']['nws_yml_options']['company'],$inputdata['head']['nws_yml_options']['delivery'],$inputdata['head']['nws_yml_options']['delivery_price']);
    $xml->createYML();
    foreach($inputdata['pages']['nws_yml_options']['pages'] as $key=>$page){
        if(isset($page['active'])){
        if($page['active']){
            $args = array(
                  'p'         => $key, // ID of a page, post, or custom type
                'post_type' => 'page'
            );
            $query = new WP_Query;
            $my_posts = $query->query($args);

                foreach( $my_posts as $pst ){
                $params = array(
                    'url' => get_permalink($pst->ID),
                    'price' => $page['price'],
                    'picture' => get_the_post_thumbnail_url( $pst->ID,'full'),
                    'delivery' => $inputdata['head']['nws_yml_options']['delivery'],
                    'local_delivery_cost' => $inputdata['head']['nws_yml_options']['delivery_price'],
                    'name' => $pst->post_title,
                    'description' => wp_trim_words(esc_attr(wp_strip_all_tags(strip_shortcodes($pst->post_content))),20,'...'),
                );
                $xml->addOffer($params,$pst->ID);
            }
        }
        }
    }

    $paths[]=array('path'=>$path,'name'=>$filename);
    $xml->saveYML($path);
    }
    $ansver = array();
    
    foreach($paths as $path){
    if (file_exists($path['path'])) {
        $ansver['text'] .= "В последний раз файл " . $path['name'] . " был изменен: " . date ("d/m/Y H:i:s.", filemtime($path['path']))."<br>";
    } else {
        $ansver['text'] .= "Файл ".$path['name']." не создан или что-то пошло не так<br>";
    }
    }
    
    $url = get_site_url(); 
    foreach($paths as $path){$ansver['link'] .= $url.'/'.$path['name'].'<br>';}

     
    
	echo json_encode($ansver, JSON_UNESCAPED_UNICODE);

	wp_die(); 
}
?>