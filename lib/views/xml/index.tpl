<?php include LIB.'/views/head.php' ?>
<div
	class="container-fluid">
	<div class="masthead">
		<h3 class="muted">
			<?= APPNAME; ?>
		</h3>
		<?php include UP . DS . 'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
<script type="text/javascript">
$(document).ready(function() {
 
  $.ajax({ type: "GET", url: "../uploads/xml/menu.xml", dataType: "xml",

    success: function(xml) {
      var out = "<ul>";
      $(xml).find('submenu').each(function() {
        var titolo = $(this).find('label').text();
        var link_markup = '<li>'+titolo+'</li>';
        out += link_markup;
      });
      out+="</ul>";     
      $('#menu').empty().append(out);
    },
    error: function(request, error, tipo_errore) { alert(error+': '+ tipo_errore); }
  });
});
</script>

<div id="menu"></div>
<?php include LIB.'/views/footer.php' ?>