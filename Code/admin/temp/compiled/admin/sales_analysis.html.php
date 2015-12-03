<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,WebCalendar.js,echarts-plain.js')); ?>
<div class="form-div">
    <form action="" method="post" id="searchForm" name="searchForm">
        <?php echo $this->_var['lang']['start_date']; ?>&nbsp;&nbsp;
        <input type="text" name="start_date" value="<?php echo $this->_var['start_date']; ?>" onclick="SelectDate(this,'yyyy-MM-dd')" readonly="true" style="width:100px;cursor:pointer" />
        <?php echo $this->_var['lang']['end_date']; ?>&nbsp;&nbsp;
        <input type="text" name="end_date" value="<?php echo $this->_var['end_date']; ?>" onclick="SelectDate(this,'yyyy-MM-dd')" readonly="true" style="width:100px;cursor:pointer" />
        <input type="submit" name="submit" value="<?php echo $this->_var['lang']['access_query']; ?>" class="button" />
    </form>
</div>
<?php endif; ?>
<div style="background: #ffffff;margin: 10px 0px;">
    <div id="graphic" class="col-md-8">
        <div id="main" class="main" style="width: 100%;height: 500px;">

        </div>
    </div>
</div>
<script type="text/javascript">
    var report ='<?php echo $this->_var['report']; ?>';
    var reportArr = report.split(',');
    var aSeries = [];
    var cateory = [];
    <?php $_from = $this->_var['dataInfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['list']):
?>
    sCategory = "<?php echo $this->_var['list']['cate']; ?>";
    cateory.push(sCategory);
    var d = {};
    d.name = sCategory;
    d.type = 'line';
    d.stack = '销售总额';
        var data = [];
        <?php $_from = $this->_var['list']['Info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'info');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['info']):
?>
        data.push('<?php echo $this->_var['info']; ?>');
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    d.data = data;
    aSeries.push(d);
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    
    var domMain = document.getElementById('main');
    option = {
        tooltip : {
            trigger: 'axis'
        },
        legend: {
            data:cateory
        },
        toolbox: {
            show : true,
            feature : {
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : reportArr
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series :aSeries
    };
    var myChart = echarts.init(domMain,'infographic');
    setTimeout(function(){
        myChart.setOption(option,false);
    },500)


    
</script>
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
