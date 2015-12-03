//载入签名控件
function writeSignActivxObject(basepath)
{
    document.write('<div id="kpsign" style="display:none">');
    if((navigator.userAgent.indexOf("MSIE") > -1) || ((navigator.userAgent.indexOf("rv:11") > -1 && navigator.userAgent.indexOf("Firefox")<= -1)))
    {
        //alert("IE");
        if((navigator.platform =="Win32"))
        {
            //alert("windows 32 bit IE");
            document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:D54C3C5F-6CA8-440B-A4E5-8B43186D5DFF" codebase='+basepath+'npkoaliiCZB_x86.CAB VIEWASTEXT>')
        }
        else if(navigator.platform =="Win64")
        {
            //alert("windows 64 bit IE");
            document.write('<OBJECT id="doit" height=40 width=200 classid="CLSID:40BEE6CE-DC27-45DB-A07E-42A625547B45"  codebase='+basepath+'npkoaliiCZB_x64.CAB VIEWASTEXT>')
        }
    }
    document.write('<param name="DigitalSignature" value="1">');
    document.write('<param name="SignMethod" value="2">');
    document.write('</OBJECT>');
    document.write('</div>');
}

//组装签名原数据
function getSourceData()
{
    var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
    sourceData += '<M><k>内部数据签名</k><v></v></M>';
    sourceData += '</D><E>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('signVersion') + '</k><v>' + changeSpecialChsForGetOriginData('ZJWC1.0') + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('rawData') + '</k><v>' + changeSpecialChsForGetOriginData('所得税的所得税的') + '</v></M>';
    sourceData += '</E></T>';
    form1.sourceData.value = sourceData;
}

//提交合同签名 flag：0买方，1卖方
function koalSign4submitContract(flag, data)
{
    var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('提交合同签名') + '</k><v></v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('合同号') + '</k><v>' + changeSpecialChsForGetOriginData(data.contractNo) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('订单号') + '</k><v>' + changeSpecialChsForGetOriginData(data.orderNo) + '</v></M>';
    sourceData += '</D><E>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('signVersion') + '</k><v>' + changeSpecialChsForGetOriginData(data.signVersion) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('timeTamp') + '</k><v>' + changeSpecialChsForGetOriginData(data.timeTamp) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('buyerCstno') + '</k><v>' + changeSpecialChsForGetOriginData(data.buyerCstno) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('buyerAccno') + '</k><v>' + changeSpecialChsForGetOriginData(data.buyerAccno) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('buyerbookSum') + '</k><v>' + data.buyerbookSum + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('salerCstno') + '</k><v>' + changeSpecialChsForGetOriginData(data.salerCstno) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('salerAccno') + '</k><v>' + changeSpecialChsForGetOriginData(data.salerAccno) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('salerbookSum') + '</k><v>' + data.salerbookSum + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('allGoodsMoney') + '</k><v>' + changeSpecialChsForGetOriginData(data.allGoodsMoney) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('tranID') + '</k><v>' + changeSpecialChsForGetOriginData(data.tranID) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('extraData') + '</k><v>' + changeSpecialChsForGetOriginData(data.extraData) + '</v></M>';
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
    	signData.msg = '您未插入浙商银行的Ukey证书!';
    } else {
    	signData.msg = '签名错误，错误码:' + doit.Kii_GetLastError();
    }
    return signData;
}

//取消合同签名 flag：0买方，1卖方
function koalSign4cancelContract(flag, data)
{
    var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('取消合同签名') + '</k><v></v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('合同号') + '</k><v>' + changeSpecialChsForGetOriginData(data.contractNo) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('订单号') + '</k><v>' + changeSpecialChsForGetOriginData(data.orderNo) + '</v></M>';
    sourceData += '</D><E>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('merId') + '</k><v>' + changeSpecialChsForGetOriginData(data.merId) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('timeTamp') + '</k><v>' + changeSpecialChsForGetOriginData(data.timeTamp) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('buyerCstno') + '</k><v>' + changeSpecialChsForGetOriginData(document.getElementById('buyerCstno')) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('salerCstno') + '</k><v>' + changeSpecialChsForGetOriginData(document.getElementById('salerCstno')) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('operate') + '</k><v>' + changeSpecialChsForGetOriginData(data.operate) + '</v></M>';
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
    	signData.msg = '您未插入浙商银行的Ukey证书!';
    } else {
    	signData.msg = '签名错误，错误码:' + doit.Kii_GetLastError();
    }
    return signData;
}

//内部数据签名
function koalSign4zjwcCheck(data)
{
    var sourceData = '<?xml version="1.0" encoding="UTF-8"?><T><D>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('内部数据签名') + '</k><v></v></M>';
    sourceData += '</D><E>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('signVersion') + '</k><v>' + changeSpecialChsForGetOriginData(data.signVersion) + '</v></M>';
    sourceData += '<M><k>' + changeSpecialChsForGetOriginData('rawData') + '</k><v>' + changeSpecialChsForGetOriginData(data.rawData) + '</v></M>';
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
    	signData.msg = '您未插入浙商银行的Ukey证书!';
    } else {
    	signData.msg = '签名错误，错误码:' + doit.Kii_GetLastError();
    }
    return signData;
}

//转义特殊字符
function changeSpecialChsForGetOriginData(SourceV)
{
    if(SourceV == null) return SourceV;
    SourceV += "";
    SourceV = SourceV.replace(/&/g,"&amp;");
    SourceV = SourceV.replace(/</g,"&lt;");
    SourceV = SourceV.replace(/>/g,"&gt;");
    SourceV = SourceV.replace(/'/g,"&apos;");
    SourceV = SourceV.replace(/"/g,"&quot;");
    return SourceV;
}
