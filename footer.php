<?php 
  $site_path = get_bloginfo("stylesheet_directory");
  
  if ( is_singular('detalle') ){
    global $site_title;
    global $site_address;
    global $location;
  }else{
		$site_title   = get_the_title();
	  $site_address = 'Dirección de el lugar....';
	  $location['lat'] = 17.533074;
	  $location['lng'] = -99.49381;
  }
?>	
	</div><!-- /.container wrap-->
	<!-- footer -->
  <footer>
      <div class="container">
      	<div class="row footer">
      		<div class="col-md-3 contacto">
              <?php echo do_shortcode('[contact-form-7 id="102" title="CONTACTANOS"]'); ?>
      		</div>
          <div class="col-md-5" style="padding: 0px;">                    
            <div id="map-mun"></div>
          </div>
      		<div class="col-md-4">
      			<div class="row">
      				<div class="col-md-12">
      					<ul class="list-inline telefonos">
      						<li>
      							<a href="#!">
      								Emergencia <span>066</span>
      							</a>
      						</li>
      						<li>
      							<a href="#!">
      								Denuncia <span>089</span>
      							</a>
      						</li>
      						<li>
      							<a href="#!">
      								Atención <br><span>(747) ##-####</span>
      							</a>
      						</li>
      					</ul>
      				</div>
              <div class="col-md-12">
                <p><?php echo $mun_address; ?></p>
              </div>
              <div class="col-md-12 text-left">
                <?php  
                  $in_header = false; 
                  set_query_var( 'in_header', $in_header );
                  get_template_part('parts/home', 'sociales'); 
                ?>
              </div>
      			</div>
      		</div>
      	</div>
      </div>
      <div class="row bg-dark">
          <div class="col-md-12 text-center">
              <h6>Sitio Web Desarrollado por César Contreras ©2015-2021</h6>
          </div>
      </div>
  </footer><!-- end Footer -->
	<!-- script references -->	
	<script src="<?php echo $site_path; ?>/js/jquery-1.11.3.min.js"></script>
	<script src="<?php echo $site_path; ?>/js/slippry.min.js"></script>
	<script src="<?php echo $site_path; ?>/js/bootstrap.min.js"></script>

	<script src="<?php echo $site_path; ?>/js/jquery.jcarousel.min.js"></script>
	<script src="<?php echo $site_path; ?>/js/site-scripts.min.js"></script>
    
    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDFUtq79-WMk25prWIKsJlHuM5f3VPl6Yk&sensor=false"></script>
    <script type="text/javascript">
        (function ($)
        {
          function map_load() 
          {
            var lat = <?php echo $location['lat']; ?>;
            var lng = <?php echo $location['lng']; ?>;
            
            // coordinates to latLng
            var latlng = new google.maps.LatLng(lat, lng);
            
            // map Options
            var myOptions = {
              zoom: 15,
              center: latlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP,
              disableDefaultUI: true
            };
            
            //draw a map
            var map = new google.maps.Map(document.getElementById("map-mun"), myOptions);
            
            var contentString = '<h3><?php echo strtoupper( esc_attr($site_title) ) ?></h3><?php echo $site_address; ?>';

            var infowindow = new google.maps.InfoWindow({
            content: contentString
            });
    
            var marker = new google.maps.Marker({
              position: latlng,
              map: map,
              animation: google.maps.Animation.DROP,
              title: '<?php echo esc_attr($site_title) ?>'
            });
            
            marker.addListener('click', function()
            {
              infowindow.open(map, marker);
            });
            
            infowindow.open(map, marker);
          }    
          
          $(document).ready(function()
          {
            // print map
            map_load();
          });
        })(jQuery);
    </script>

    <?php wp_footer(); ?>
</body>
</html>