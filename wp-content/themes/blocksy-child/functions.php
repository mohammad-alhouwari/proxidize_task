<?php

if (!defined('WP_DEBUG')) {
	die('Direct access forbidden.');
}

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});

function ti_custom_javascript()
{
?>
    <script>
        jQuery(document).ready(function($) {
            const searchForm = $('form.ct-search-form');
            const searchInput = searchForm.find('input[type="search"]');
            
            searchForm.on('submit', function(event) {
                event.preventDefault(); 
                
                $.ajax({
                    type: "GET",
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: {
                        action: 'search_api_proxy',
                        query: searchInput.val()
                    },
                    success: function(response) {
                        const inputElement = document.querySelector('.modal-field');
                        const inputEvent = new Event('input', {
                            bubbles: true,
                            cancelable: true,
                        });
                        inputElement.dispatchEvent(inputEvent);
                        console.log(response);
                        
                        
                        searchForm.off('submit').submit();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                       
                    }
                });
            });
        });
        
    </script>
<?php
}
add_action('wp_head', 'ti_custom_javascript');

add_action('wp_ajax_search_api_proxy', 'search_api_proxy');
add_action('wp_ajax_nopriv_search_api_proxy', 'search_api_proxy');

function search_api_proxy()
{
	$query = isset($_GET['query']) ? $_GET['query'] : '';
	$url = 'http://e1.proxidize.com:5500/search?query=' . urlencode($query);

	$response = wp_remote_get($url);

	if (!is_wp_error($response)) {
		$body = wp_remote_retrieve_body($response);
		$products_data = json_decode($body, true);

		if (isset($products_data['products']) && is_array($products_data['products'])) {
			foreach ($products_data['products'] as $product) {
				$name = $product['name'];
				$price = $product['price'];
				$category_names = explode(';;', $product['category']); 

				$existing_product = get_page_by_title($name, OBJECT, 'product');

				if (!$existing_product) {
					$new_product = array(
						'post_title' => $name,
						'post_content' => '',
						'post_status' => 'publish',
						'post_type' => 'product',
					);

					$product_id = wp_insert_post($new_product);

					update_post_meta($product_id, '_regular_price', $price);
					update_post_meta($product_id, '_price', $price);

					$term_ids = array();
					foreach ($category_names as $category_name) {
						$term = get_term_by('name', $category_name, 'product_cat');
						if (!$term) {
							$term = wp_insert_term($category_name, 'product_cat');
						}
						$term_ids[] = $term->term_id;
					}
					wp_set_object_terms($product_id, $term_ids, 'product_cat');
				}
			}
		}

		wp_send_json_success($body);
	} else {
		wp_send_json_error($response->get_error_message());
	}

	wp_die();
}
