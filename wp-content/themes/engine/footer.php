<?php
#determine which columns to display
$col1 = __('Footer Column 1',IT_TEXTDOMAIN);
$col2 = __('Footer Column 2',IT_TEXTDOMAIN);
$col3 = __('Footer Column 3',IT_TEXTDOMAIN);
$col4 = __('Footer Column 4',IT_TEXTDOMAIN);
$class = 'widgets';
$disable_footer = it_get_setting('footer_disable');

if(!(it_get_setting('footer_disable') && it_get_setting('subfooter_disable'))) {
?>

    <div id="footer" class="container-fluid no-padding builder-section builder-widgets">
            
        <div class="row">
    
            <div class="col-md-12">
            
            	<?php echo it_background_ad(); #full screen background ad ?>
        
                <div class="container-inner">

					<?php if(it_get_setting('ad_footer')!='') { #footer ad ?>
                        
                        <div class="row it-ad" id="it-ad-footer">
                            
                            <div class="col-md-12">
                            
                                <?php echo do_shortcode(it_get_setting('ad_footer')); ?>  
                                
                            </div>                    
                              
                        </div>
                    
                    <?php } ?>

					<?php if(!it_get_setting('footer_disable')) { ?>
                        
                        <div class="widgets-inner shadowed">
                            
                            <div class="row">
                            
                                <div class="widget-panel left col-md-3">
        
									<?php echo it_widget_panel($col1, $class); ?>
                                    
                                </div>
                            
                                <div class="widget-panel mid mid-left col-md-3">
                                
                                    <?php echo it_widget_panel($col2, $class); ?>
                                    
                                </div>
                                
                                <br class="clearer hidden-lg hidden-md" />
                            
                                <div class="widget-panel mid mid-right col-md-3">
                                
                                    <?php echo it_widget_panel($col3, $class); ?>
                                    
                                </div> 
                                
                                <div class="widget-panel right col-md-3">
                                
                                    <?php echo it_widget_panel($col4, $class); ?>
                                    
                                </div> 
                                
                            </div> 
                            
                        </div>                            
                        
                    <?php } ?>
                    
                    <?php if(!it_get_setting('subfooter_disable')) { ?>
                    
                    	<div class="subfooter shadowed">
                    
                            <div class="row">
                                
                                <div class="col-sm-6 copyright">
                                
                                    <?php if(it_get_setting('copyright_text')!='') { ?>
                                    
                                        <?php echo it_get_setting('copyright_text'); ?>
                                        
                                    <?php } else { ?>
                                    
                                        <?php _e( 'Copyright', IT_TEXTDOMAIN ); ?> &copy; <?php echo date("Y").' '.get_bloginfo('name'); ?>,&nbsp;<?php _e( 'All Rights Reserved.', IT_TEXTDOMAIN ); ?>
                                    
                                    <?php } ?>  
                                    
                                </div>
                                
                                <div class="col-sm-6 credits">
                                
                                    <?php if(it_get_setting('credits_text')!='') { ?>
                                    
                                        <?php echo it_get_setting('credits_text'); ?>
                                        
                                    <?php } else { ?>
                                    
                                        <?php _e( 'Fonts by', IT_TEXTDOMAIN); ?> <a href="https://www.google.com/fonts/"><?php _e( 'Google Fonts', IT_TEXTDOMAIN); ?></a>. <?php _e( 'Icons by', IT_TEXTDOMAIN); ?> <a href="http://fontello.com/"><?php _e( 'Fontello', IT_TEXTDOMAIN); ?></a>. <?php _e( 'Full Credits', IT_TEXTDOMAIN); ?> <a href="<?php echo CREDITS_URL; ?>"><?php _e( 'here &raquo;', IT_TEXTDOMAIN); ?></a>
                                    
                                    <?php } ?>                         
                                
                                </div>
                            
                            </div>
                            
                        </div>
                        
                    <?php } ?>
                    
				</div> <!--/container-inner-->
                
            </div> <!--/col-md-12-->
            
        </div> <!--/row-->
        
    </div> <!--/container-fluid-->
    
    <?php } ?>

</div> <!--/after-header-->

<?php do_action('it_body_end'); ?>
<?php wp_footer(); ?>
<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
var _tmr = window._tmr || (window._tmr = []);
_tmr.push({id: "3082096", type: "pageView", start: (new Date()).getTime()});
(function (d, w, id) {
  if (d.getElementById(id)) return;
  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
  ts.src = "https://top-fwz1.mail.ru/js/code.js";
  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
})(document, window, "topmailru-code");
</script><noscript><div>
<img src="https://top-fwz1.mail.ru/counter?id=3082096;js=na" style="border:0;position:absolute;left:-9999px;" alt="Top.Mail.Ru" />
</div></noscript>
<!-- //Rating@Mail.ru counter -->

<script type='text/javascript' src='https://tehno.guru/wp-content/themes/engine/js/scrl.js'></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script type="text/javascript" async>
  (function (w) {
    function start() {
      w.removeEventListener("YaMarketAffiliateLoad", start);
w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget1',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
      searchSelector: "#marketWidget1>s",       
			searchMatch: 'exact',      
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget2',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget2>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget3',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget3>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget4',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget4>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget5',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget5>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget6',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget6>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget7',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget7>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget8',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget8>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget9',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget9>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget10',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget10>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget11',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget11>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget12',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget12>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget13',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget13>s", 
			searchMatch: 'exact',
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget14',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget14>s", 
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget15',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget15>s", 
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget16',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget16>s", 
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget17',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget17>s", 
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget18',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget18>s", 
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget19',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget19>s", 
          searchCount: 4,
          vid: 3
        }
      });
    w.YaMarketAffiliate.createWidget({
        containerId: 'marketWidget20',
        type: 'offers',
        params: {
          clid: 2356248,
          themeId: 10,
          searchSelector: "#marketWidget20>s", 
          searchCount: 4,
          vid: 3
        }
      });
  }
    w.YaMarketAffiliate
      ? start()
      : w.addEventListener("YaMarketAffiliateLoad", start);
  })(window);
</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" async>
  (function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
      try {
        w.yaCounter47590078 = new Ya.Metrika2({
          id:47590078,
          clickmap:true,
          trackLinks:true,
          accurateTrackBounce:true,
          webvisor:true
        });
      } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
      s = d.createElement("script"),
      f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = "https://mc.yandex.ru/metrika/tag.js";

    if (w.opera == "[object Opera]") {
      d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
  })(document, window, "yandex_metrika_callbacks2");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/47590078" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>

</html>
