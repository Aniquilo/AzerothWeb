<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
   <!-- BODY Content end here -->
   </div>
  </div>
 </div>
 <!-- BODY-->

 <!-- Footer -->
        
              <div class="footer">

                <p>Juega gratis ahora</p>
              <div><img class="line2" src="<?=base_url()?>/template/style/images/line.png"/></div>
    
              <div><a  class="play2" href="<?=base_url()?>/downloads">Descargar</a></div> 
    
              <div class="logo-footer"><img src="<?=base_url()?>/template/style/images/logo-footer.png"/></div>
    
              <!--language dropdown-->

              <!--<div class="language">
     
              <a class="dropbtn">English<img class="drop-icon" src="<?=base_url()?>/template/style/images/drop-icon.png"/></a>
    
              <div class="dropdown-content">
    
              <a href="#">русский</a>
              <a href="#">中国人</a>
              <a href="#">Português</a>
              <a href="#">Español</a>
              </div>
              
              </div>-->
        
              <div class="footer-end">
        
              <div class="footer-link">

              <a href="<?=base_url()?>/support/rules">Reglas</a>         
              <a href="<?=base_url()?>/support/tos">Términos de Uso</a>
              <a href="<?=base_url()?>/support/cookie_policy">COOKIE POLICY</a>  
              </div>    
            
              <div><p>© Copyright 2022 Azeroth-Project.com</p></div>
            

</div>
</div>
<!-- Footer.End -->


</section>
<!-- Section.End -->
 
        <script>
    function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}
 
    function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}
    </script> 

    <script>

    var video = document.getElementById("header-video");

    </script>
    <script src="<?=base_url()?>/template/style/bootstrap/css/bootstrap.min.js"></script>
    <script src="<?=base_url()?>/template/js/sidenav.js"></script></body>
 
<?php
    //Add the default footer js include
    $CORE->tpl->AddFooterJs('template/js/jquery.selecttransform.js?v=1');
    $CORE->tpl->AddFooterJs('template/js/jquery.loadingbar.js');
    $CORE->tpl->AddFooterJs('template/js/tooltips.js');
    $CORE->tpl->AddFooterJs('template/js/footer.js');

	//Print the Javascript loader
    $CORE->tpl->PrintFooterJavascripts();
?>

<script type="text/javascript" src="<?=wowdb_url()?>/static/widgets/power.js?lang=<?=wowdb_lang()?>"></script>
<script>var aowow_tooltips = { "colorlinks": true, "iconizelinks": false, "renamelinks": false };</script>
<script>
    function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}
 
    function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}
    </script> 

    <script>

    var video = document.getElementById("header-video");

    </script>
<script src="<?=base_url()?>/template/js/vendor.min.js"></script>
<script src="<?=base_url()?>/template/js/bootstrap/bootstrap.min.js"></script>
<script src="<?=base_url()?>/template/js/main_home.min.js"></script>

</body>
</html>