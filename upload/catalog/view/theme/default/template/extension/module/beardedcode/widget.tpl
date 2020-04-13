<?php if ( $ajax === 1 ) { ?>
    <div id="js_widget_three_page">
        <div class="js_preload">
            <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function start() {
                $.get('index.php?route=extension/module/beardedcode_three_page&ajax=true', function (response) {
                    $('#js_widget_three_page .js_preload').remove();
                    $('#js_widget_three_page').html(response);
                });
            }
			<?php if ($timer > 0) { ?>
            setTimeout(start, <?php echo $timer; ?>);
			<?php } else { ?>
            start();
			<?php }?>
        }, false);
    </script>
<?php } else { ?>
    <div style="display: none;"><svg version="1.1" id="i_arrow_right" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 511.995 511.995" style="enable-background:new 0 0 511.995 511.995;" xml:space="preserve"><g><g><path d="M381.039,248.62L146.373,3.287c-4.083-4.229-10.833-4.417-15.083-0.333c-4.25,4.073-4.396,10.823-0.333,15.083 L358.56,255.995L130.956,493.954c-4.063,4.26-3.917,11.01,0.333,15.083c2.063,1.979,4.729,2.958,7.375,2.958 c2.813,0,5.604-1.104,7.708-3.292L381.039,263.37C384.977,259.245,384.977,252.745,381.039,248.62z" /></g></g></svg></div>

    <div class="row">
		<?php foreach( $widget as $column ) { ?>
            <div class="col-lg-4 col-md-6">
                <div class="widget">
					<?php if ( $column['title'] ) { ?>
                        <h3 class="widget-title"><?php echo $column['title']; ?></h3>
					<?php } ?>
					<?php foreach( $column['products'] as $product ) { ?>
                        <div class="widget-media">
                            <a class="widget-image-link" href="<?php echo $product['href']; ?>">
                                <img width="64" src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
                            </a>
                            <div class="media-body">
                                <h6 class="widget-product-title">
                                    <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                                </h6>
                                <div class="widget-product-meta">
									<?php if ( $product['price'] ) { ?>
										<?php if ( $product['special'] ) { ?>
                                            <span class="widget-text-accent"><?php echo $product['special']; ?></span>
                                            <del class="widget-text-muted"><?php echo $product['price']; ?></del>
										<?php } else { ?>
                                            <span class="widget-text-accent"><?php echo $product['price']; ?></span>
										<?php } ?>
									<?php } ?>
                                </div>
                            </div>
                        </div>
					<?php } ?>
					<?php if ( $column['button_title'] ) { ?>
                        <p class="widget-further-text">...</p>
                        <a class="widget-further-link" href="<?php echo $column['link']; ?>">
							<?php echo $column['button_title']; ?>
                            <svg><use x="0" y="0" xlink:href="#i_arrow_right" /></svg>
                        </a>
					<?php } ?>
                </div>
            </div>
		<?php } ?>
    </div>
<?php } ?>