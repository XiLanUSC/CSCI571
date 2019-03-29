<?php
if ($_GET['task'] == 1) {
    $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
    $version = '1.0.0';  // API version supported by your application
    $appid = 'XiLan-csci571-PRD-316de56b6-2423a42c';  // Replace with your own AppID
    $globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
    $query = $_GET['key'];  // You may want to supply your own query
    $safequery = urlencode($query);  // Make the query URL-friendly

    $i = '0';  // Initialize the item filter index to 0

    // Create a PHP array of the item filters you want to use in your request

    $filterarray = array();

    $condarray = array();
    $hasCon = false;
    //search only for new staff
    if (isset($_GET['new'])) {
        array_push($condarray, 'New');
        $hasCon = true;
    }
    //search only for old staff
    if (isset($_GET['used'])) {
        array_push($condarray, 'Used');
        $hasCon = true;
    }
    //doesn't matter what to search
    if (isset($_GET['unspecified'])) {
        array_push($condarray, 'Unspecified');
        $hasCon = true;
    }
    if ($hasCon == true) {
        array_push($filterarray, array(
            'name' => 'Condition',
            'value' => $condarray,
            'paramName' => '',
            'paramValue' => ''
        ));
    }
    //seach local pick up staff
    if (isset($_GET['local'])) {
        array_push($filterarray, array(
            'name' => 'LocalPickupOnly',
            'value' => true,
            'paramName' => '',
            'paramValue' => ''
        ));
    }
    //search free delivery staff
    if (isset($_GET['free'])) {
        array_push($filterarray, array(
            'name' => 'FreeShippingOnly',
            'value' => true,
            'paramName' => '',
            'paramValue' => ''
        ));
    }
    //check if near by button is checked
    if (isset($_GET['nby'])) {

        array_push($filterarray, array(
            'name' => 'LocalSearchOnly',
            'value' => true,
            'paramName' => '',
            'paramValue' => ''
        ));

        if ($_GET['location'] == 'here') {
            array_push($filterarray, array(
                'name' => 'buyerPostalCode',
                'value' => $_GET['curzip'],
                'paramName' => '',
                'paramValue' => ''
            ));
        } else {
            array_push($filterarray, array(
                'name' => 'buyerPostalCode',
                'value' => $_GET['zip'],
                'paramName' => '',
                'paramValue' => ''
            ));
        }

        array_push($filterarray, array(
            'name' => 'MaxDistance',
            'value' => $_GET['distance'],
            'paramName' => '',
            'paramValue' => ''
        ));
    }
    // Generates an indexed URL snippet from the array of item filters
    function buildURLArray($filterarray)
    {
        global $urlfilter;
        global $i;
    // Iterate through each filter in the array
        foreach ($filterarray as $itemfilter) {
        // Iterate through each key in the filter
            foreach ($itemfilter as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $j => $content) { // Index the key for each value
                        $urlfilter .= "&itemFilter($i).$key($j)=$content";
                    }
                } else {
                    if ($value != "") {
                        $urlfilter .= "&itemFilter($i).$key=$value";
                    }
                }
            }
            $i++;
        }
        return "$urlfilter";
    } // End of buildURLArray function

    // Build the indexed item filter URL snippet
    buildURLArray($filterarray);

    // Construct the findItemsByKeywords HTTP GET call 
    $apicall = "$endpoint?";
    $apicall .= "OPERATION-NAME=findItemsAdvanced";
    $apicall .= "&SERVICE-VERSION=$version";
    $apicall .= "&SECURITY-APPNAME=$appid&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD";
    $apicall .= "&GLOBAL-ID=$globalid";
    $apicall .= "&keywords=$safequery";


    if ($_GET['location'] == 'here') {
        $apicall .= "&buyerPostalCode=";
        $apicall .= $_GET['curzip'];
    } elseif ($_GET['location'] == 'there') {
        $apicall .= "&buyerPostalCode=";
        $apicall .= $_GET['zip'];
    }
    $apicall .= "&paginationInput.entriesPerPage=20";
    if (isset($_GET['cate']) and $_GET['cate'] != 'All') {
        $apicall .= "&categoryId=" . $_GET['cate'];
    }
    $apicall .= "$urlfilter";
    // Load the call and capture the document returned by eBay API
    $json = file_get_contents($apicall);
    echo $json;
    exit;
} elseif ($_GET['task'] == 2) {
    $apicallIdem = "http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON&appid=XiLan-csci571-PRD-316de56b6-2423a42c&siteid=0&version=967&ItemID=";
    $apicallIdem .= $_GET['itemid'] . "&IncludeSelector=Description,Details,ItemSpecifics";

    $json1 = file_get_contents($apicallIdem);

    echo $json1;
    exit;
} elseif ($_GET['task'] == 3) {
    error_reporting(E_ALL);
    $apicallSimilar = "http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0&CONSUMER-ID=XiLan-csci571-PRD-316de56b6-2423a42c&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&itemId=";
    $apicallSimilar .= $_GET['itemid'] . "&maxResults=8";

    $json2 = file_get_contents($apicallSimilar);
    echo $json2;
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
    .main {
        height: 400px;
        width: 800px;
        border: 5px solid #ccc;
        margin: 0 auto;
        padding: 0 20px;
    }

    .main .top {
        text-align: center;
        font-size: 50px;
        height: 70px;
        font-style: italic;
        /* font-weight: bolder; */
        border-bottom: 3px solid #ccc;
        font-family: "Times New Roman", Times, serif;
    }

    .main .buttom {
        margin: 0 10px;
        font-size: 20px;
        font-family: "Times New Roman", Times, serif;
    }

    .main .buttom .items {
        height: 50px;
        line-height: 50px;
    }

    #c1,
    #c2,
    #c3 {
        margin-left: 18px;
    }

    #s1,
    #s2 {
        margin-left: 30px;
    }

    #mf,
    #heretext,
    #dis,
    ::placeholder {
        color: rgba(0, 0, 0, 0.3);
    }

    .tooltiptext1 {
        visibility: hidden;
        width: 132px;
        height: 25px;
        line-height: 2;
        background-color: #fff;
        color: #000;
        text-align: center;
        border-radius: 6px;
        position: absolute;
        border: 1px solid red;
        z-index: 1;
        top: 40px;
        font-size: 10px;
        opacity: 0;
        transition: opacity 2s;
    }

    .tooltiptext2 {
        visibility: hidden;
        width: 132px;
        height: 25px;
        line-height: 2;
        background-color: #fff;
        color: #000;
        text-align: center;
        border-radius: 6px;
        position: absolute;
        border: 1px solid red;
        z-index: 1;
        top: 100%;
        font-size: 10px;
        opacity: 0;
        transition: opacity 2s;
    }

    #message {
        width: 1200px;
        background-color: #ccc;
        margin: 0 auto;
        text-align: center;
        margin-top: 30px;
        border: 2px solid gray;
    }

    #resArea {
        text-align: center;
        margin: 0 auto;
    }

    #bigtitle {
        text-align: center;
        font-weight: 700;
    }

    #box1,
    #box2,
    #box1-1,
    #box2-1 {
        width: 250px;
        text-align: center;
        color: rgba(0, 0, 0, 0.3);
        margin: 20px auto 0;
    }
    </style>
</head>

<body>
    <div class="main">
        <div class="top">
            Product Search
        </div>
        <div class="buttom">
            <div class="items">
                <strong>Keyword </strong>
                <div style="display:inline-block; position:relative">
                    <input type="text" id="keyword" name="key" />
                    <span class="tooltiptext1">Please fill out this field</span>
                </div>
            </div>
            <div class="items">
                <strong>Category</strong>
                <select id="category" name="cate">
                    <option value="All">All Categories</option>
                    <option value="550">Art</option>
                    <option value="2984">Baby</option>
                    <option value="267">Books</option>
                    <option value="11450">Clothing, Shoes & Accessories</option>
                    <option value="58058">Computer/Tablets & Networking</option>
                    <option value="26395">Health & Beauty</option>
                    <option value="11233">Music</option>
                    <option value="1249">Video Games & Consoles</option>
                </select>
            </div>
            <div class="items">
                <strong>Condition</strong>
                <input type="checkbox" value="New" id="c1" name="new" />New
                <input type="checkbox" value="Used" id="c2" name="used" />Used
                <input type="checkbox" value="Unspecified" id="c3" name="unspecified" />Unspecified
            </div>
            <div class="items">
                <strong>Shipping Options</strong>
                <input type="checkbox" value="Local" id="s1" name="local" />Local
                Pickup <input type="checkbox" value="Free" id="s2" name="free" />Free
                Shipping
            </div>
            <div style="margin-top:10px; height:50px">
                <div style="float:left">
                    <input type="checkbox" id="nby" name="nby" value="Nby" />
                    <strong>Enable Nearby Search</strong>
                </div>
                <div style="float:left; margin-left: 20px">
                    <input type="text" name="distance" disabled value="10" id="dis" />
                    <strong><span id="mf">miles from</span></strong>
                </div>
                <div style="float:left; margin-left:5px">
                    <input type="radio" name="location" value="here" id="hr" checked disabled />
                    <span id="heretext">Here</span><br />
                    <input name="curzip" type="hidden" value="00000" id="cur" />
                    <input type="radio" name="location" value="zip" id="zip" disabled />
                    <div style="display:inline-block; position:relative">
                        <input type="text" name="code" id="code" disabled placeholder="zip code" />
                        <span class="tooltiptext2">Please fill out this field</span>
                    </div>
                </div>
            </div>

            <div style="margin-left: 330px;">
                <button id="search" disabled>Search</button>
                <button id="clearbtn">Clear</button>
            </div>
        </div>
    </div>
    <div id="resultArea">
    </div>
</body>
<script>
var locationUrl = "http://ip-api.com/json";
var content = JSON.parse(getJson(locationUrl));
var curZip = content["zip"];
var srchBtn = document.getElementById("search");
srchBtn.disabled = false;

function getJson(url) {
    var xhr = new XMLHttpRequest();
    var jsonDoc;
    xhr.open("GET", url, false);
    xhr.send();
    if (xhr.status == 200) {
        jsonDoc = xhr.responseText;
        console.log(jsonDoc);
    }
    return jsonDoc;
}

var keyword = document.getElementById("keyword");
var category = document.getElementById("category");
var nw = document.getElementById("c1");
var used = document.getElementById("c2");
var unspec = document.getElementById("c3");
var local = document.getElementById("s1");
var free = document.getElementById("s2");
var nbtBtn = document.getElementById("nby");
var dis = document.getElementById("dis");
var hr = document.getElementById("hr");
var zip = document.getElementById("zip");
var code = document.getElementById('code');
var clearBtn = document.getElementById("clearbtn");
var resArea = document.getElementById("resultArea");
var mf = document.getElementById("mf");
var heretext = document.getElementById("heretext");
var tip1 = document.getElementsByClassName('tooltiptext1')[0]
var tip2 = document.getElementsByClassName('tooltiptext2')[0]
cur.value = curZip;
srchBtn.onclick = function() {
    resArea.innerHTML = "";
    if (!keyword.value) {
        tip1.style.visibility = "visible";
        tip1.style.opacity = '1';
        keyword.focus();
        setTimeout(function() {
            tip1.style.visibility = "hidden";
            tip1.style.opacity = '0';
        }, 1000);
        return false;
    }
    if (nbtBtn.checked && zip.checked && !code.value) {
        tip2.style.visibility = "visible";
        tip2.style.opacity = '1';
        code.focus();
        setTimeout(function() {
            tip2.style.visibility = "hidden";
            tip2.style.opacity = '0';
        }, 1000);
        return false;
    }
    if (nbtBtn.checked && zip.checked) {
        if (code.value.length != 5 || !parseInt(code.value)) {
            resArea.innerHTML = "<div id='message'>Zipcode is invalid</div>"
            return false;
        }
    }
    var fetchURL = `?task=1&key=${keyword.value}&cate=${category.value}`;
    if (nw.checked) fetchURL += '&new=New';
    if (used.checked) fetchURL += '&used=Used';
    if (unspec.checked) fetchURL += '&unspecified=Unspecified';
    if (local.checked) fetchURL += '&local=Local';
    if (free.checked) fetchURL += '&free=Free';
    if (nbtBtn.checked) {
        fetchURL += `&nby=Nby&distance=${dis.value}`;
        if (hr.checked) {
            fetchURL += `&location=here&curzip=${curZip}`;
        } else {
            fetchURL += `&location=there&zip=${code.value}`;
        }
    }
    var data = JSON.parse(getJson(fetchURL));
    if (data.findItemsAdvancedResponse[0].searchResult[0]['@count'] == 0) {
        resArea.innerHTML = "<div id='message'>No Records has been found</div>"
        return false;
    }
    var table = document.createElement('table');
    var tr = document.createElement('tr');
    var head = ['Index', 'Photo', 'Name', 'Price', 'Zip code', 'Condition', 'Shipping Option'];
    for (var j = 0; j < head.length; j++) {
        var th = document.createElement('th');
        var txt = document.createTextNode(head[j]);
        th.style.border = "2px solid #ccc";
        th.appendChild(txt);
        tr.appendChild(th);
    }
    table.appendChild(tr);
    for (var j = 0; j < data.findItemsAdvancedResponse[0].searchResult[0].item.length; j++) {
        tr = document.createElement('tr');
        var td = document.createElement('td');
        var index = j + 1;
        var node = document.createTextNode(index);
        td.style.border = "1px solid #ccc";
        td.appendChild(node);
        tr.appendChild(td);
        var cur = data.findItemsAdvancedResponse[0].searchResult[0].item[j];

        td = document.createElement('td');
        if (!'galleryURL' in cur || !cur.galleryURL) {
            node = document.createTextNode('N/A');
            td.appendChild(node);
        } else {
            var img = document.createElement('img');
            img.src = cur.galleryURL[0];
            td.appendChild(img);
        }
        td.style.border = "1px solid #ccc";
        tr.appendChild(td);

        var names = [cur.title[0], cur.sellingStatus[0].currentPrice[0].__value__];
        for (var k = 0; k < names.length; k++) {
            td = document.createElement('td');
            if (k == 0) {
                var link = document.createElement('a');
                link.classList.add("detail");
                link.innerText = names[k];
                td.appendChild(link);
            } else if (k == 1) {
                node = document.createTextNode('$' + names[k]);
                td.appendChild(node);
            }
            // else if(k == 3){
            //     if(!'postalCode' in cur || !cur.postalCode){
            //         node = document.createTextNode('N/A');
            //     }else{
            //         node = document.createTextNode(names[k]);
            //         td.appendChild(node);
            //     }
            // }
            // else {
            //     node = document.createTextNode(names[k]);
            //     td.appendChild(node);
            // }
            td.style.border = "1px solid #ccc";
            tr.appendChild(td);
        }
        td = document.createElement('td');
        if (!'postalCode' in cur || !cur.postalCode) {
            node = document.createTextNode('N/A');
        } else {
            node = document.createTextNode(cur.postalCode[0]);
        }
        td.appendChild(node);
        td.style.border = "1px solid #ccc";
        tr.appendChild(td);

        td = document.createElement('td');
        if (!'condition' in cur || !cur.condition || cur.condition.length == 0) {
            node = document.createTextNode('N/A');
        } else {
            node = document.createTextNode(cur.condition[0].conditionDisplayName[0]);
        }
        td.appendChild(node);
        td.style.border = "1px solid #ccc";
        tr.appendChild(td);


        td = document.createElement('td');
        if (!'shippingInfo' in cur || !'shippingServiceCost' in cur.shippingInfo[0] || !cur.shippingInfo || !cur
            .shippingInfo[0].shippingServiceCost) {
            node = document.createTextNode('N/A');
        } else {
            node = document.createTextNode(cur.shippingInfo[0].shippingServiceCost[0].__value__ == 0 ?
                'Free Shipping' : '$' + cur
                .shippingInfo[0].shippingServiceCost[0].__value__);
        }
        td.appendChild(node);
        td.style.border = "1px solid #ccc";
        tr.appendChild(td);
        table.appendChild(tr);
    }
    resArea.appendChild(table);
    table.style.border = "1px solid #ccc";
    table.style.marginTop = "20px";
    var detailLink = document.getElementsByClassName('detail');
    for (var l = 0; l < detailLink.length; l++) {
        let ll = l;
        detailLink[ll].onmouseover = function() {
            detailLink[ll].style.color = "gray";
        }
        detailLink[ll].onmouseout = function() {
            detailLink[ll].style.color = "black";
        }
        detailLink[ll].onclick = function() {
            var cur = data.findItemsAdvancedResponse[0].searchResult[0].item[ll];
            var thirdUrl = `?task=3&itemid=${cur.itemId[0]}`;
            var secondUrl = `?task=2&itemid=${cur.itemId[0]}`;
            var itemData = JSON.parse(getJson(secondUrl));
            var similarData = JSON.parse(getJson(thirdUrl));
            renderItemDetail(itemData, similarData);
        }
    }
}

function renderItemDetail(itemData, similarData) {
    window.scrollTo(0, 0);
    var resArea = document.getElementById('resultArea');
    resArea.innerHTML = '<h1 id="bigtitle">Item Details<h1>';
    var tt = document.createElement('table');
    if (itemData.Item.PictureURL) {
        var tr = document.createElement('tr');
        var td = document.createElement('td');
        var txt = document.createTextNode('Photo');
        td.appendChild(txt);
        td.style.border = "1px solid black";
        tr.appendChild(td);
        td = document.createElement('td');
        var img = document.createElement('img');
        img.src = itemData.Item.PictureURL[0];
        img.style.width = "800px";
        td.appendChild(img);
        td.style.border = "1px solid black";
        tr.appendChild(td);
        tt.appendChild(tr);
    }
    if (itemData.Item.Title) addLine(itemData.Item.Title, 'Title', tt);
    if (itemData.Item.Subtitle) addLine(itemData.Item.Subtitle, 'Subtitle', tt);
    if (itemData.Item.CurrentPrice) addLine(itemData.Item.CurrentPrice.Value + itemData.Item
        .CurrentPrice.CurrencyID, 'Price', tt);
    if (itemData.Item.Location && itemData.Item.PostalCode) addLine(
        `${itemData.Item.Location},${itemData.Item.PostalCode}`, 'Location', tt);
    if (itemData.Item.Seller.UserID) addLine(itemData.Item.Seller.UserID, 'Seller', tt);
    if (itemData.Item.ReturnPolicy) {
        if (itemData.Item.ReturnPolicy.ReturnsAccepted == 'Returns Accepted') addLine(
            `Returns Accepted within ${itemData.Item.ReturnPolicy.ReturnsWithin}`,
            'Return Policy (US)', tt);
        else addLine('Returns Not Accepted', 'Return Policy (US)', tt);
    }
    if (itemData.Item.ItemSpecifics) {
        for (var i = 0; i < itemData.Item.ItemSpecifics.NameValueList.length; i++) {
            var spec = itemData.Item.ItemSpecifics.NameValueList[i];
            addLine(spec.Value, spec.Name, tt);
        }
    }
    resArea.appendChild(tt);
    tt.style.border = "1px solid black";
    tt.style.margin = "0 auto";
    tt.style.marginTop = "10px";
    tt.style.width = "850px";
    var but = document.createElement('div');
    resArea.appendChild(but);

    but.innerHTML =
        "<div id='box1'><div>click to show seller message</div><img src='down.jpg' style='height:20px'></div><div id='showArea'></div><div id='box2'><div>click to show similar items</div><img src='down.jpg' style='height:20px'></div><div id='similarArea'></div>";
    var box1 = document.getElementById('box1');
    var box2 = document.getElementById('box2');
    box1.onclick = function() {
        var showArea = document.getElementById('showArea');
        if (box1.id == 'box1') {
            if (box2.id == 'box2-1') {
                similarArea.innerHTML = '';
                box2.id = 'box2';
                box2.innerHTML = "<div>click to show similar items</div><img src='down.jpg' style='height:20px'>"
            }
            if (!itemData.Item.Description) {
                showArea.innerHTML = "<div id='message'>No Seller Message Found</div>";
            } else {
                var frame = document.createElement('iframe');
                frame.srcdoc = itemData.Item.Description;
                frame.style.margin = "0 auto";
                frame.style.display = "block";
                frame.style.overflow = "hidden";
                frame.style.border = "none";
                frame.onload = function() {
                    frame.style.height = frame.contentDocument.documentElement.scrollHeight +
                        'px';
                    frame.style.width = frame.contentDocument.documentElement.scrollWidth +
                        'px';
                }
                showArea.appendChild(frame);
                box1.id = 'box1-1';
                box1.innerHTML =
                    "<div>click to hide seller message</div><img src='up.jpg' style='height:20px'>"
            }
        } else {
            showArea.innerHTML = "";
            box1.id = "box1";
            box1.innerHTML =
                "<div>click to show seller message</div><img src='down.jpg' style='height:20px'>"
        }
    }
    box2.onclick = function() {
        var similarArea = document.getElementById('similarArea');
        if (box2.id == 'box2') {
            if (box1.id == 'box1-1') {
                showArea.innerHTML = "";
                box1.id = "box1";
                box1.innerHTML =
                    "<div>click to show seller message</div><img src='down.jpg' style='height:20px'>"
            }
            if (!similarData.getSimilarItemsResponse.itemRecommendations.item || similarData.getSimilarItemsResponse
                .itemRecommendations.item.length == 0) {
                similarArea.innerHTML = "<div id='message'>No Similar Item Found</div>"
            } else {
                var target = similarData.getSimilarItemsResponse.itemRecommendations.item;
                var horibar = document.createElement('div');
                horibar.style.border = "1px solid gray";
                horibar.style.margin = "0 auto";
                horibar.style.height = '400px';
                horibar.style.width = '800px';
                horibar.style.overflowX = "auto";
                horibar.style.overflowY = "hidden";
                horibar.style.whiteSpace = "nowrap";
                for (let index = 0; index < target.length; index++) {
                    var sim = document.createElement('div');
                    sim.style.display = "inline-block";
                    sim.style.height = '300px';
                    sim.style.width = '200px';
                    sim.style.marginLeft = "20px";
                    sim.style.textAlign = "center";
                    var pic = document.createElement('img');
                    pic.style.width = "200px";
                    pic.src = target[index].imageURL;
                    pic.style.display = "block";
                    sim.appendChild(pic);
                    var name = document.createElement('div');
                    name.classList.add('nn');
                    name.innerHTML = target[index].title;
                    name.style.marginTop = "10px";
                    name.style.width = "200px"
                    name.style.whiteSpace = "normal";
                    var price = document.createElement('div');
                    price.marginTop = "10px";
                    price.innerHTML = '$' + target[index].buyItNowPrice.__value__;
                    price.style.fontWeight = "700";
                    sim.appendChild(name);
                    sim.appendChild(price);
                    name.onclick = function() {
                        var newURL1 = `?task=2&itemid=${target[index].itemId}`;
                        var newURL2 = `?task=3&itemid=${target[index].itemId}`;
                        var itemD = JSON.parse(getJson(newURL1));
                        var similarD = JSON.parse(getJson(newURL2));
                        renderItemDetail(itemD, similarD);
                    }
                    horibar.appendChild(sim);
                }
                similarArea.appendChild(horibar);
                var nn = document.getElementsByClassName('nn');
                for (let index = 0; index < nn.length; index++) {
                    nn[index].onmouseover = function() {
                        nn[index].style.color = 'gray';
                    }
                    nn[index].onmouseout = function() {
                        nn[index].style.color = 'black';
                    }
                }
                box2.id = 'box2-1';
                box2.innerHTML =
                    "<div>click to hide similar item</div><img src='up.jpg' style='height:20px'>"
            }
        } else {
            similarArea.innerHTML = '';
            box2.id = 'box2';
            box2.innerHTML = "<div>click to show similar items</div><img src='down.jpg' style='height:20px'>"
        }
    }
}

function addLine(ele, front, tt) {
    var tr = document.createElement('tr');
    var td = document.createElement('td');
    var txt = document.createTextNode(front);
    td.appendChild(txt);
    td.style.border = "1px solid black";
    tr.appendChild(td);
    td = document.createElement('td');
    txt = document.createTextNode(ele);
    td.appendChild(txt);
    td.style.border = "1px solid black";
    tr.appendChild(td);
    tt.appendChild(tr);
}
nbtBtn.onclick = function() {
    if (nbtBtn.checked) {
        dis.disabled = false;
        hr.disabled = false;
        zip.disabled = false;
        mf.style.color = "rgb(0, 0, 0)";
        heretext.style.color = "rgb(0,0,0)";
        dis.style.color = "rgb(0,0,0)";
        if (zip.checked) {
            code.disabled = false;
            code.style.color = "rgb(0,0,0)";
        }
    } else {
        dis.disabled = true;
        hr.disabled = true;
        zip.disabled = true;
        code.disabled = true;
        mf.style.color = "rgba(0, 0, 0, 0.3)";
        heretext.style.color = "rgba(0,0,0, 0.3)";
        dis.style.color = "rgba(0,0,0,0.3)";
        code.style.color = "rgba(0,0,0,0.3)";
    }
};
zip.onclick = function() {
    code.disabled = false;
    code.style.color = "rgb(0,0,0)";
};
hr.onclick = function() {
    code.disabled = true;
    code.style.color = "rgba(0,0,0,0.3)";
};
clearBtn.onclick = function() {
    resArea.innerHTML = "";
    keyword.value = "";
    category.options[0].selected = true;
    nw.checked = false;
    used.checked = false;
    unspec.checked = false;
    free.checked = false;
    local.checked = false;
    nby.checked = false;
    dis.value = 10;
    dis.disabled = true;
    hr.checked = true;
    hr.disabled = true;
    zip.disabled = true;
    code.disabled = true;
    code.value = "";
    code.placeholder = "zip code";
    mf.style.color = "rgba(0, 0, 0, 0.3)";
    heretext.style.color = "rgba(0,0,0, 0.3)";
    dis.style.color = "rgba(0,0,0,0.3)";
    code.style.color = "rgba(0,0,0,0.3)";
};
</script>

</html>
