<?php if(!empty($stats)): ?>
<div id="chart1" class="charts"></div>

<script type="text/javascript">

    var protesters = <?php echo($js);?>;
    //var line1=[['2008-06-30 8:00AM',4], ['2008-7-30 8:00AM',6.5], ['2008-8-30 8:00AM',5.7], ['2008-9-30 8:00AM',9], ['2008-10-30 8:00AM',8.2]];
      var plot2 = $.jqplot('chart1', [protesters], {
          title:'Number of protesters',
          gridPadding:{right:65},
          axes:{
            xaxis:{
              renderer:$.jqplot.DateAxisRenderer,
              tickOptions:{formatString:'%b %#d, %y'},                                          
            },
            yaxis:{
                min:0
            }
          },
          series:[{lineWidth:2, markerOptions:{style:'filledCircle',lineWidth:1,size:5}}]
      });
    
  
</script>
<?php else: ?>
<div>There is no statistics for the moment</div>
<?php endif; ?>