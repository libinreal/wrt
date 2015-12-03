define(function(require) {
	var sign = {};
	
	//载入签名控件
	sign.writeSignActivxObject = function() {
		basepath = "";
        document.write('<div id="kpsign" style="display:none">');
        if((navigator.userAgent.indexOf("MSIE") > -1) || ((navigator.userAgent.indexOf("rv:11") > -1 && navigator.userAgent.indexOf("Firefox")<= -1)))
        {
            //alert("IE");
            if((navigator.platform =="Win32")) {
                //alert("windows 32 bit IE");
                document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:D54C3C5F-6CA8-440B-A4E5-8B43186D5DFF" codebase='+basepath+'npkoaliiCZB_x86.CAB VIEWASTEXT>')
            } else if(navigator.platform =="Win64") {
                //alert("windows 64 bit IE");
                document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45"  codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
            }
        }
        document.write('<param name="DigitalSignature" value="1">');
        document.write('<param name="SignMethod" value="2">');
        document.write('</OBJECT>');
        document.write('</div>');
	};
	
	//组装签名原数据
    sign.getSourceData = function () {
        var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
        sourceData += '<M><k>内部数据签名</k><v></v></M>';
        sourceData += '</D><E>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('signVersion') + '</k><v>' + this.changeSpecialChsForGetOriginData('ZJWC1.0') + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('rawData') + '</k><v>' + this.changeSpecialChsForGetOriginData('所得税的所得税的') + '</v></M>';
        sourceData += '</E></T>';
        form1.sourceData.value = sourceData;
    };

    //提交合同签名 flag：0买方，1卖方
    sign.koalSign4submitContract = function (flag, data) {
        var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('提交合同签名') + '</k><v></v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('合同号') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.contractNo) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('订单号') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.orderNo) + '</v></M>';
        sourceData += '</D><E>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('signVersion') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.signVersion) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('timeTamp') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.timeTamp) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('buyerCstno') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.buyerCstno) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('buyerAccno') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.buyerAccno) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('buyerbookSum') + '</k><v>' + data.buyerbookSum + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('salerCstno') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.salerCstno) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('salerAccno') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.salerAccno) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('salerbookSum') + '</k><v>' + data.salerbookSum + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('allGoodsMoney') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.allGoodsMoney) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('tranID') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.tranID) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('extraData') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.extraData) + '</v></M>';
        sourceData += '</E></T>';

        doit.Kii_SelectCertByDn('OU','CZB');
        var seErr = doit.Kii_GetLastError();
        if(seErr == 1005) return false;
        var hr = doit.Kii_P7_Sign(sourceData,0);
        var signData = {
        	success : 0
        };
        if (hr == 0) {
            //get signed data in the form of P7
        	signData.success = 1;
            signData.data = doit.Kii_GetSignData();
        } else if(hr == 1004) {
        	signData.errorInfo = '您未插入浙商银行的Ukey证书!';
        } else {
        	signData.errorInfo = '签名错误，错误码:' + doit.Kii_GetLastError();
        }
        return signData;
    };

    //取消合同签名 flag：0买方，1卖方
    sign.koalSign4cancelContract = function (flag, data) {
        var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('取消合同签名') + '</k><v></v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('合同号') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.contractNo) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('订单号') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.orderNo) + '</v></M>';
        sourceData += '</D><E>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('merId') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.merId) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('timeTamp') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.timeTamp) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('buyerCstno') + '</k><v>' + this.changeSpecialChsForGetOriginData(document.getElementById('buyerCstno')) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('salerCstno') + '</k><v>' + this.changeSpecialChsForGetOriginData(document.getElementById('salerCstno')) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('operate') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.operate) + '</v></M>';
        sourceData += '</E></T>';

        doit.Kii_SelectCertByDn('OU', 'CZB');
        var seErr = doit.Kii_GetLastError();
        if(seErr == 1005) return false;
        var hr = doit.Kii_P7_Sign(sourceData,0);
        var signData = {
            	success : 0
            };
        if (hr == 0) {
            //get signed data in the form of P7
        	signData.success = 1;
            signData.data = doit.Kii_GetSignData();
        } else if(hr == 1004) {
        	signData.errorInfo = '您未插入浙商银行的Ukey证书!';
        } else {
        	signData.errorInfo = '签名错误，错误码:' + doit.Kii_GetLastError();
        }
        return signData;
    };

    //内部数据签名
    sign.koalSign4zjwcCheck = function (data) {
        var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('内部数据签名') + '</k><v></v></M>';
        sourceData += '</D><E>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('signVersion') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.signVersion) + '</v></M>';
        sourceData += '<M><k>' + this.changeSpecialChsForGetOriginData('rawData') + '</k><v>' + this.changeSpecialChsForGetOriginData(data.rawData) + '</v></M>';
        sourceData += '</E></T>';

        doit.Kii_SelectCertByDn("OU","CZB");
        var seErr = doit.Kii_GetLastError();
        if(seErr == 1005) return false;
        var hr = doit.Kii_P7_Sign(sourceData,0);
        var signData = {
            	success : 0
            };
        if (hr == 0) {
            //get signed data in the form of P7
        	signData.success = 1;
            signData.data = doit.Kii_GetSignData();
        } else if(hr == 1004) {
        	signData.errorInfo = '您未插入浙商银行的Ukey证书!';
        } else {
        	signData.errorInfo = '签名错误，错误码:' + doit.Kii_GetLastError();
        }
        return signData;
    };

    //转义特殊字符
    sign.changeSpecialChsForGetOriginData = function (SourceV) {
        if(SourceV == null) return SourceV;
        SourceV += "";
        SourceV = SourceV.replace(/&/g,"&amp;");
        SourceV = SourceV.replace(/</g,"&lt;");
        SourceV = SourceV.replace(/>/g,"&gt;");
        SourceV = SourceV.replace(/'/g,"&apos;");
        SourceV = SourceV.replace(/"/g,"&quot;");
        return SourceV;
    };
    return sign;
});