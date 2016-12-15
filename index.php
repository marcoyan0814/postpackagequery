<?php
	/**************************************************************
	 Name: PHP 5 時間轉換
	**************************************************************/
	if (phpversion()!='4.3.9'){
		date_default_timezone_set( "Asia/Taipei" );
	}
	
	set_time_limit(0);
	ini_set("memory_limit","2048M");
	

	//設定語系
	@header('Content-Type: text/html; charset=utf-8');
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	if($_POST)
	{
		if($_POST['act']=="query" && $_POST['mailid']!="")
		{
			$url = "http://sprws.post.gov.tw/psttp_mailquery.asmx?wsdl";
			$client = new SoapClient($url);
		
			$params = array(
			  "MAIL_NO" => $_POST['mailid']
			);
			$response = $client->__soapCall("MailQueryNewStatus", array($params));
			
			$xml=simplexml_load_string($response->MailQueryNewStatusResult) or die("Error: Cannot create object");
			
			$xmlArr = json_decode(json_encode($xml), TRUE);
			$resArr = $xmlArr['ITEM'];
			
			//arrayPrint($resArr);
			$responseArr = array();
	
			//處理空值
			for($i = 0; $i<count($resArr); $i++)
			{
				if(!is_array($resArr[$i]['MAILNO']))
				{
					$responseArr[] = $resArr[$i];
				}
			}
			
			echo json_encode($responseArr);
			exit;

		}
		exit;
	}
	

function arrayPrint($arrayName,$flag=''){
	if($flag==true){
		ob_start();
			echo "<br><pre>";
		print_r($arrayName);
		echo "</pre><br>";
			$str = ob_get_clean();
			return $str;
	}else{
		echo "<br><pre>";
		print_r($arrayName);
		echo "</pre><br>";
	}
}

?>
<!doctype html>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>郵件查詢</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<!--css spinners-->
<link rel="stylesheet" href="css/heartbeat.css" type="text/css">
</head><body>
<div class="container theme-showcase" role="main">
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default" style="margin-top:10px;">
        <div class="panel-body">
          <div class="form-group">
            <label>請輸入郵件號碼（每行一筆）</label><span class="pull-right"><button type="button" class="btn btn-primary" id="btnQuery" name="btnQuery">立即查詢</button> <button type="button" class="btn btn-primary" onClick="location.href='?t='+Math.random();" name="btnClear">重新查詢</button></span>
            
            <textarea name="mailno" cols="40" rows="10" class="form-control" id="mailno" placeholder="請輸入郵件號碼" style="margin-top:10px;"></textarea>
          </div>
        </div>
      </div>
      <!--
      <div class="panel panel-default" style="margin-top:10px;">
        <div class="panel-body">
          <div class="form-group">
            <label>查詢結果</label>
            <textarea name="res" cols="40" rows="3" class="form-control" id="res" placeholder="查詢結果" style="margin-top:10px;" readonly></textarea>
          </div>
        </div>
      </div>
      -->
    </div>
    <div class="col-md-6">
      <div class="box"> 
        <!-- /.box-header -->
        <div class="box-body table-responsive"> 
          <!--
            <div><span class="text-danger" id="resultMsg" name="resultMsg"></span></div>
            <div class="fixed-table-toolbar"><div class="bars pull-left"><div id="toolbar">
                <button onclick="goAction('delete');" id="removeBtn" class="btn btn-danger"><i class="fa fa-remove"></i> 刪除</button>
                <button class="btn btn-default" onclick="$('#resultTable').bootstrapTable('checkAll');">全選</button>
                <button class="btn btn-default" onclick="$('#resultTable').bootstrapTable('uncheckAll');">取消全選</button>
                <button onclick="goAction('export');" class="btn btn-default">匯出</button>
                <button class="btn btn-primary" onclick="location.reload();">顯示全部</button>
                <span class="input-group col-md-4"> <span class="input-group-addon">抽選筆數</span>
        <input type="text" placeholder="請輸入人數..." value="5" name="num" id="num" class="form-control">
        <span class="input-group-btn">
                <button onclick="randomData();" type="button" class="btn btn-danger">立即抽選!</button>
                </span> </span> </div></div><div class="pull-right search"><input type="text" placeholder="搜尋" class="form-control"></div></div>
                -->
          <table id="resultTable" name="resultTable" data-search="true" data-advanced-search="false" data-id-table="advancedTable" data-toggle="table" data-detail-view="false" data-detail-formatter="detailFormatter">
            <thead style="background-color:#FFC;">
              <tr class="warning"> 
                <!--<th data-field="chkid" data-checkbox="true">id</th>-->
                <th data-field="MAILNO" data-sortfield="MAILNO" data-align="left" data-sortable="true">郵件號碼</th>
                <th data-field="DATIME" data-sortfield="DATIME" data-align="center" data-sortable="true" class="col-md-2">處理時間</th>
                <th data-field="STATUS" data-sortfield="STATUS" data-align="left" data-sortable="true">狀態</th>
                <th data-field="BRHNC" data-sortfield="BRHNC" data-align="left" data-sortable="true">處理單位</th>
                <!--<th data-field="func" data-align="left" data-formatter="funcFormatter" data-events="funcEvents" class="col-md-2">功能</th>--> 
              </tr>
            </thead>
          </table>
          <input type="hidden" id="sortstr" name="sortstr" value="" />
          <input type="hidden" id="startid" name="startid" value="" />
          <input type="hidden" id="tblname" name="tblname" value="#resultTable" />
        </div>
        <!-- /.box-body --> 
      </div>
      <!-- /.box --> 
    </div>
    <!-- /.col-xs-12 --> 
  </div>
</div>
<!-- /container --> 

<!-- Add jQuery library --> 
<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script> 
<script type="text/javascript" src="js/bootstrap.min.js"></script> 
<script type="text/javascript" src="js/bootbox.min.js"></script> 
<!--blockUI--> 
<script type="text/javascript" src="js/BlockUI/jquery.blockUI.js"></script> 
<!--Bootstrap Table-->
<link rel="stylesheet" href="js/bootstrap-table-1.10.1/bootstrap-table-c.css">
<script type="text/javascript" src="js/bootstrap-table-1.10.1/bootstrap-table-c.js"></script> 
<script type="text/javascript" src="js/bootstrap-table-1.10.1/locale/bootstrap-table-zh-TW.min.js"></script> 
<!-- Bootstrap Table Plugin Exports --> 
<script type="text/javascript" src="js/bootstrap-table-1.10.1/extensions/export/bootstrap-table-export.js"></script> 
<script src="js/bootstrap-table-1.10.1/extensions/export/tableExport.js"></script> 
<script src="js/bootstrap-table-1.10.1/extensions/export/jquery.base64.js"></script> 
<!--<script src="js/bootstrap-table-1.10.1/extensions/toolbar/bootstrap-table-toolbar.min.js"></script> -->
<script type="text/javascript">
//查詢筆數
var querynum = 0;
var qstr = "";
var j = 0;

$(document).ready(function() {
	//初始化表格
	var tblname = "#resultTable";
	/*
	var data = [];
				data.push({
					"MAILNO": "96621322027818",
					"STATUS": "郵件投遞中"+Math.random(),
					"BRHNC": "台北郵局大安投遞股",
					"DATIME": "20160909084514"
				});
	*/
	$(tblname).bootstrapTable('destroy').bootstrapTable({
		data: "",
		//url: "http://192.168.16.211/jwa/admin/jobs/getDataList.html",
		contentType: "application/x-www-form-urlencoded",
		//sidePagination: "server",
		//iconsPrefix: "fa",	//user fontawesome
		//method: "post",
		cache: false,
		undefinedText: "",	//若為空值顯示
		sortName: "DATIME",
		sortOrder: "desc",
		clickToSelect: false,	//click to select
		uniqueId: 'sort',	//tr unique id
		idField: 'id',	//checkbox id值
		smartDisplay: true,	//顯示card mode
		cardView: false,	//顯示card mode
		pageSize: 30,	//預設筆數
		pagination: true,	//啟用分頁
		//showRefresh: true,	//啟用重整按鈕
		showToggle: true,	//啟用切換card或table
		showColumns: true,	//顯示欄位切換
		search: true,	//啟用搜尋功能
		selectItemName: "chk[]",	//The name of radio or checkbox input.
		pageList: "[10, 30, 100, 200]",	//分頁數量
		toolbar: "#toolbar",	//toolbar
		showExport: true,
		exportTypes: ['excel','csv','txt'],
		queryParams: function (params) {
			return {
				limit: params.limit,
				offset: params.offset,
				search: params.search,
				sort: params.sort,
				order: params.order,
				"q": $('#q').val(),
				"cc": $('#cc :selected').val(),
				"ctime": "",
				"act": "getList"
			};
		},
		exportDataType: "all",
		exportOptions: {
			consoleLog: "true"
		}
	});	
	

	//查詢
	$('#btnQuery').on("click",function(){
		var mailid = $('#mailno').val();
		qstr = "";
		j = 0;
		
		if(mailid=="")
		{
			blockPage("off");
			bootbox.alert("請輸入郵件號碼",function(){ $('#mailno').focus(); });
			return false;
		}
		
		$('#btnQuery').attr("disabled","disabled");
		

		var arr = mailid.split("\n");
		for(var i=0;i<=arr.length;i++)
		{
			if((j!=0 && (j%5)==0) || i==arr.length)
			{
				//ajax query
				blockPage();
				
				$('#btnQuery').text("查詢中，請稍等...");
				$('#btnQuery').attr("class","btn btn-danger");
	
				$.ajax({
					type: 'POST',
					async: false,
					cache: false,
					timeout: 100000,
					url: "index.php",
					data: {
						'act': "query",
						'mailid': qstr
					},
					dataType: 'json',
					error: function(){
						return true;
					},
					success: function(data){
						$(tblname).bootstrapTable('append', data);
						
						//init
						qstr = "";
						$j = 0;
					}
				});
			}

			if(arr[i] && arr[i].trim()!="")
			{
				qstr += arr[i].trim()+",";
			}
			j++;
		}

		$('#btnQuery').removeAttr("disabled");
		blockPage("off");
		$('#btnQuery').text("立即查詢");
		$('#btnQuery').attr("class","btn btn-primary");
		bootbox.alert("郵件資料已全數查詢完畢");
	});
	
	//清除
	$('#btnClear').on("click",function(){
		$('#mailno').val('');
		$('#res').val('');
		$(tblname).bootstrapTable('removeAll');
	});
});

//遮罩
function blockPage(act)
{
	if(act=='off')
	{
		$.unblockUI();
	}
	else
	{
		$.blockUI({ 
			  message: "<div class='heartbeat-loader'> Loading… </div><div style='font-size:12px; color:#fff; margin-top:10px; min-width:60px; text-align:center;'>資料處理中，請耐心等候</div>",
			  css: {   
				'z-index': 9999,
				border: 'none',   
				padding: '15px',   
				backgroundColor: 'none',
				'-webkit-border-radius': '10px',   
				'-moz-border-radius': '10px',   
				opacity: .9  
			}}); 
	}
}
</script>
</body>
</html>
