<link rel="stylesheet" href="/js/morris.js-0.4.3/morris.css">
<script src="/js/jquery-ui/jquery-1.9.1.js"></script>
<script src="/js/raphael-min.js"></script>
<script src="/js/morris.js-0.4.3/morris.min.js"></script>
<script type="text/javascript">
$(function() {
	Morris.Bar({
		  element: 'analytics4hours',
		  data: [
		    { y: '00', a: 100 },
		    { y: '01', a: 75 },
		    { y: '02', a: 50 },
		    { y: '03', a: 75 },
		    { y: '04', a: 50 },
		    { y: '05', a: 75 },
		    { y: '06', a: 100 }
		  ],
		  xkey: 'y',
		  ykeys: ['a'],
		  labels: ['Hours']
		});
});
</script>
<div id="analytics4hours"></div>

