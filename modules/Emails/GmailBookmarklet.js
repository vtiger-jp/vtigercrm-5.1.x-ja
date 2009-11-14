function init(){
	function XHR(type,url,params){
		this.xhr = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		this.url = url;
		this.params = params;
		this.type = type;
	};
	XHR.prototype.load=function(async,callback){
		this.onload=callback;
		this.async=async;
		
		if(this.type.toLowerCase()=="get"){
			this.url = this.url+"?"+this.params;
		}
		
		this.xhr.open(this.type,this.url,this.async);
		try{
			if(async){
				var request = this;
				this.xhr.onreadystatechange=function(){
					request.readyStateChange();
				}
			}
			if(this.type.toLowerCase()=="post"){
				this.xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				this.xhr.setRequestHeader("Content-length", this.params.length);
			}
			this.xhr.send(this.params);
		}catch(e){
			alert("error: "+e);
		}
	};
	XHR.prototype.readyStateChange=function(){
		var ready=this.xhr.readyState;
		if(ready==4){
			var httpStatus=null;
			httpStatus=this.xhr.status;
			if(httpStatus==200 || httpStatus==0){
				if(this.async){
					this.onload.call(this,this.xhr);
				}
			}
		}
	};
	
	function VtigerWebservice(vtigerurl,username,accesskey){
		var vtigerURL=vtigerurl;
		var username=username;
		var accessKey=accesskey;
		var userId=null;
		var sessionId=null;
		
		function getResult(response){
			return response.result;
		}
		
		function getError(response){
			return response.error;
		}
		
		function extendsession(){
			var req = new XHR("post",vtigerURL,"operation=extendsession");
			req.load(false);
			var res = JSON.parse(req.xhr.responseText);
			if(res.success){
				var result = getResult(res);
				this.userId=result.userId;
				this.sessionId=result.sessionName;
			}
		}
		
		function create(object, objectType, callback){
			var objectJson = JSON.stringify(object);
			var req = new XHR("post",doc.vtigerURL,("operation=create&elementType="+objectType+"&sessionName="+this.sessionId+
				"&element="+encodeURIComponent(objectJson)));
			req.load(true,callback);
		}
		
		function query(query, callback){
			var req = new XHR("get",doc.vtigerURL,"operation=query&query="+encodeURIComponent(query)+"&sessionName="+this.sessionId);
			req.load(true,callback);
		}
		return {extendSession:extendsession,query:query,getResult:getResult,userId:userId,create:create,getError:getError};
	};
	if (!this.JSON) {
		JSON = function () {
			function f(n) {
				return n < 10 ? "0" + n : n;
			}
			Date.prototype.toJSON = function () {
				return this.getUTCFullYear()   + "-" +
					 f(this.getUTCMonth() + 1) + "-" +
					 f(this.getUTCDate())	  + "T" +
					 f(this.getUTCHours())	 + ":" +
					 f(this.getUTCMinutes())   + ":" +
					 f(this.getUTCSeconds())   + "Z";
			};
			var m = {
				"\b": "\\b",
				"\t": "\\t",
				"\n": "\\n",
				"\f": "\\f",
				"\r": "\\r",
				"\"" : "\\\"",
				"\\": "\\\\"
			};
			function stringify(value, whitelist) {
				var a,		  // The array holding the partial texts.
					i,		  // The loop counter.
					k,		  // The member key.
					l,		  // Length.
					r = /["\\\x00-\x1f\x7f-\x9f]/g,
					v;		  // The member value.
				switch (typeof value) {
				case "string":
					return r.test(value) ?
						"\"" + value.replace(r, function (a) {
							var c = m[a];
							if (c) {
								return c;
							}
							c = a.charCodeAt();
							return "\\u00" + Math.floor(c / 16).toString(16) +
													   (c % 16).toString(16);
						}) + "\"" :
						"\"" + value + "\"";
				case "number":
					return isFinite(value) ? String(value) : "null";
				case "boolean":
				case "null":
					return String(value);
				case "object":
					if (!value) {
						return "null";
					}
					if (typeof value.toJSON === "function") {
						return stringify(value.toJSON());
					}
					a = [];
					if (typeof value.length === "number" &&
							!(value.propertyIsEnumerable("length"))) {
						l = value.length;
						for (i = 0; i < l; i += 1) {
							a.push(stringify(value[i], whitelist) || "null");
						}
						return "[" + a.join(",") + "]";
					}
					if (whitelist) {
						l = whitelist.length;
						for (i = 0; i < l; i += 1) {
							k = whitelist[i];
							if (typeof k === "string") {
								v = stringify(value[k], whitelist);
								if (v) {
									a.push(stringify(k) + ":" + v);
								}
							}
						}
					} else {
						for (k in value) {
							if (typeof k === "string") {
								v = stringify(value[k], whitelist);
								if (v) {
									a.push(stringify(k) + ":" + v);
								}
							}
						}
					}
					return "{" + a.join(",") + "}";
				}
							return undefined;
			}
			return {
				stringify: stringify,
				parse: function (text, filter) {
					var j;
					function walk(k, v) {
						var i, n;
						if (v && typeof v === "object") {
							for (i in v) {
								if (Object.prototype.hasOwnProperty.apply(v, [i])) {
									n = walk(i, v[i]);
									if (n !== undefined) {
										v[i] = n;
									}
								}
							}
						}
						return filter(k, v);
					}
					if (/^[\],:{}\s]*$/.test(text.replace(/\\./g, "@").
	replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(:?[eE][+\-]?\d+)?/g, "]").
	replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
						j = eval("(" + text + ")");
						return typeof filter === "function" ? walk("", j) : j;
					}
					throw new SyntaxError("parseJSON");
				}
			};
		}();
	}
	
	var doc = document;
	
	function trim(str) { 
		var s = str.replace(/\s+$/, "");
		s = s.replace(/^\s+/, "");
		return s;
	}
	
	function getGmailSubject() {
		var subject = top.document.title;
		var firstIndexOf = subject.indexOf("-");
		var lastIndexOf = subject.lastIndexOf("-");
		subject = subject.substring(firstIndexOf+1, lastIndexOf-1);
		return trim(subject);
	}
	
	function getVtigerBaseURL(){
		var url = document.location.href.substring(0,document.location.href.lastIndexOf('/')+1);
		if(url.length > 0){
			if(url.charAt(url.length-1) !="/"){
				url += "/";
			}
			return url;
		}
		return null;
	}
	
	function getVtigerURL(){
		if(doc.vtigerBaseURL.length > 0){
			var url =doc.vtigerBaseURL;
			url +="webservice.php";
			return url;
		}
		return null;
	}
	
	function getGmailURL() {
		var locationhref = location.href;
		if(locationhref.indexOf("?")) {
			var lsplits = locationhref.split("?");
			locationhref = lsplits[0];
		} else if(locationhref.indexOf("#")) {
			var lsplits = locationhref.split("#");
			locationhref = lsplits[0];
		}
		return locationhref;
	}
	
	var elementId = "__vtigerBookMarkletDiv__";
	var busyElementId = "__vtigerBookMarkletDivBusy__"
	function showBookMarkletUI(){
		var bookMarkletDiv = doc.getElementById(elementId);
		if(bookMarkletDiv == null){ 
			bookMarkletDiv.style.display="block";
		}
	}
	
	function hideBookMarkletUI(){
		var bookMarkletDiv = doc.getElementById(elementId);
		if(bookMarkletDiv != null){
			bookMarkletDiv.style.display="none";
		}
	}
	
	function destroyBookMarkletUI(){
		var closeElementId = '__vtigetGmailCloseElement';
		var parentLocation = location.href.split("location=");
		if(parentLocation.length>1){
			var closeElement = doc.getElementById(closeElementId);
			if(closeElement==null){
				closeElement = doc.createElement("iframe");
				closeElement.style.width="0px";
				closeElement.frameBorder="0px";
				closeElement.style.height="0px";
				closeElement.style.display="none";
				closeElement.id = closeElementId;
				closeElement.src = decodeURIComponent(parentLocation[1])+"#";
				doc.body.appendChild(closeElement);
			}
			closeElement.onload=function(){
				eval('window.parent.parent.removeMe()');
			}
		}
	}
	
	function showBusy(){
		var bookMarkletDiv = doc.getElementById(elementId);
		var busyElem = doc.getElementById(busyElementId);
		if(busyElem==null){
			busyElem = doc.createElement('div');
			busyElem.id = busyElementId;
			busyElem.innerHTML="Working...";
			busyElem.style.position="absolute";
			busyElem.style.top="5px";
			busyElem.style.right="5px";
			busyElem.style.color="white";
			busyElem.style.backgroundColor="#D75235";
			busyElem.style.padding="2px";
			bookMarkletDiv.appendChild(busyElem);
		}else{
			busyElem.style.display="block";
		}
		
	}
	
	function hideBusy(){
		var busyElem = doc.getElementById(busyElementId);
		if(busyElem!=null){
			busyElem.style.display="none";
		}
	}
	
	if(typeof doc.vtigerURL =="undefined" || doc.vtigerURL == null || doc.vtigerURL == ""){
		doc.vtigerBaseURL = getVtigerBaseURL();
		if(doc.vtigerBaseURL ==null){
			alert("Please Provide a Valid URL");
			return;
		}
		doc.vtigerURL = getVtigerURL();
	}
	var client = new VtigerWebservice(doc.vtigerURL,null,null);
	
	showBusy();
	client.extendSession();
	hideBusy();
	showBookMarkletUI();
	
	function onReady(id,callback){
		var interval = window.setInterval(function(){
			var elem = doc.getElementById(id);
			if(elem != null && typeof elem != "undefined"){
				callback();
				window.clearInterval(interval);
			}
		},10);
	}
	
	function createBookMarkletUI(){
			
		onReady("__saveVtigerEmail__",function(){
			doc.getElementById("__saveVtigerEmail__").onclick=function(){
				createEmail();
			}
		});
		onReady("parentType",function(){
			doc.getElementById("parentName").innerHTML = "No "+doc.getElementById("parentType").value+" Selected.";
			doc.getElementById("parentType").onchange=function(){
				doc.getElementById("parentName").innerHTML = "No "+this.value+" Selected.";  
			}
		});
		
		function getQuery(searchValue){
			
			var moduleName = doc.getElementById("parentType").value;
			var moduleDetails = JSON.parse(moduleNameFields);
			var entityNameFields = moduleDetails[moduleName];
			var selectFields = '';
			var whereFields = '';
			each(entityNameFields,function(k,v){
				if(selectFields.length > 0){
					selectFields +=',';
				}
				selectFields += v;
				if(whereFields.length > 0){
					whereFields +=" or ";
				}
				whereFields += v+" like '%"+searchValue+"%'";
			});
			return "select "+selectFields+" from "+moduleName+" where "+whereFields+";";
		}
		
		onReady("__searchVtigerAccount__",function(){
			doc.getElementById("__searchVtigerAccount__").onclick=function(e){
				var elem = doc.getElementById("__vtigerAccountSearchList___");
				elem.style.display="";
				
				var accountName = doc.getElementById("__searchaccount__").value;
				if(accountName.length < 1){
					alert("Please enter the search critiria");
					return;
				}
				showBusy();
				var q = getQuery(accountName);
				var moduleName = doc.getElementById("parentType").value;
				var responseElem = doc.getElementById("__vtigerAccountSearchResponse___");
				if(responseElem != null){
					responseElem.innerHTML = '';
				}
				client.query(q,function(response){
					var responseElem = doc.getElementById("__vtigerAccountSearchResponse___");
					if(responseElem == null){
						var sibling = doc.createElement("tr");
						var td = doc.createElement("td");
						td.colSpan = "3";
						str = "<div id=\"__vtigerAccountSearchResponse___\" "+
								"style=\"width: 100%;overflow: auto;\"> </div>";
						td.innerHTML = str;
						sibling.appendChild(td);
						elem.parentNode.appendChild(sibling);
					}
					onReady("__vtigerAccountSearchResponse___",function(){
						displaySearchResult(moduleName,response,accountName);
					});
				});
			}
		});
	}
	
	function getSiblingByTagName(elem,tagName){
		var sibling = elem.nextSibling;
		while(sibling.nodeName.toLowerCase()!=tagName.toLowerCase()){
			sibling = sibling.nextSibling;
		}
		return sibling;
	}
	
	function each(object,callback){
		var name, i = 0, length = object.length;
		if ( length == undefined ) {
			for ( name in object )
				if ( callback.call( object[ name ], name, object[ name ] ) === false )
					break;
		} else
			for ( var value = object[0];
				i < length && callback.call( value, i, value ) !== false; value = object[++i] ){}
		return object;
	}
	
	function findObject(array,needle){
		var name, i = 0, length = array.length;
		var propect = null;
		for (; i < length; ++i ){
			var object = array[i];
			for ( name in object ){
				if(object[name] === needle){
					prospect=object;
					break;
				}
			}
		}	
		return prospect;
	}
	
	function getEntityName(moduleName,row){
		var moduleDetails = JSON.parse(moduleNameFields);
		var entityNameFields = moduleDetails[moduleName];
		var entityName = '';
		var entityNameField = '';
		each(entityNameFields,function(k,v){
			if(entityName.length>0){
				entityName += " ";
			}
			entityName +=row[v]; 
		});
		return entityName;
	}
	
	function displaySearchResult(moduleName,response,accountName){
		hideBusy();
		var queryResponse = JSON.parse(response.responseText);
		if(queryResponse.success == true){
			var queryResult = client.getResult(queryResponse);
			var str ="<ul class='searchResult'>";
			if(queryResult.length > 0){
				each(queryResult, function(i, row){
					var entityName = getEntityName(moduleName,row);
					str+="<li><a id=\""+row['id']+"\" class='small searchLinks'>"+
						entityName+"</a></li>";
				});
			}else{
				str +="<li>No Record Match \""+accountName+"\"</li>";
			}
			str += "</ul>";
			var elem = doc.getElementById("__vtigerAccountSearchResponse___");
			elem.style.height="120px";
			elem.innerHTML = str;
			each(elem.getElementsByTagName("a"),function(i,v){
				v.onclick=function(){
					var elem = findObject(queryResult,this.id);
					var entityName = getEntityName(moduleName,elem);
					setAccountId(elem.id,entityName);
					var wrap = doc.getElementById("__vtigerAccountSearchList___");
					wrap.style.display="none";
					doc.getElementById('__searchaccount__').value='';
				}
			});
		}else{
			var error = client.getError(queryResponse);
			alert("Vtiger returned Error: \nerrorCode: "+error.code+"\nerror Message: "+error.message);
		}
	}
	
	function waitForObject(obj,callback){
		var interval = window.setInterval(function(){
			if(typeof obj != "undefined"){
				callback();
				window.clearInterval(interval);
			}
		},10);
	}
	
	function setAccountId(id,entityName){
		var elem = doc.getElementById("parent_id");
		var elemName = doc.getElementById("parentName");
		elem.value = id;
		elemName.innerHTML = entityName;
	}
	
	function closeOnSuccess(response){
		var createResponse = JSON.parse(response.responseText);
		if(createResponse.success == true){
			alert("Email added to vtigerCRM.");
		}else{
			doc.getElementById("__saveVtigerEmail__").disabled=false;
			var error = client.getError(createResponse);
			alert("Error while creating: \nerrorCode: "+error.code+"\nerror Message: "+error.message);
		}
	}
	
	function getTodayDate(format){
		var date = new Date();
		return date.getDay()+"-"+date.getMonth()+"-"+date.getFullYear();
	}
	
	function createEmail(){
		var parent_id = doc.getElementById("parent_id").value;
		var type = doc.getElementById("parentType").value;
		if(parent_id.length < 1){
			alert("No "+type+" selected.");
			return ;
		}
		var subject = doc.getElementById("subject").value;
		if(subject.length < 1){
			alert("Please provide a value for Subject");
			return;
		}
		
		var description = doc.getElementById("description").value;
		if(description.length < 1){
			alert("Please provide a value for Body of the email.");
			return;
		}
		doc.getElementById("__saveVtigerEmail__").disabled=true;
		var email ={"description":description,"subject":subject,
			"description":description,"assigned_user_id":client.userId,
			"date_start":getTodayDate(),"activitytype":"Emails","parent_id": parent_id};
		client.create(email,"Emails",closeOnSuccess) 
	}
	createBookMarkletUI();
}
