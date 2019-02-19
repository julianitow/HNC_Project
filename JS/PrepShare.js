function UpdateCurrency(){

	var Currency = document.getElementById('Currency');
	Currency.addEventListener("change", function(){ // Add burger menu element to open
		GetShareData();
	});
}

function updateShareData() {
	var checkPosition = setInterval(GetShareData,10000); // Sets the run interval to 10sec
}

//*********************************************************
// Gets the data from the json file and updates the share data elements
// Ian Holderness
// V1.0 - 14/02/2018
// Tested - 14/02/2018
//*********************************************************
//;
//var strUser = e.options[e.selectedIndex].value;
function GetShareData() {
	//*********************************************************
	// Gets the data from the json file and updates the share data elements
	// Ian Holderness
	// V1.01 - 14/02/2018
	// Tested - 14/02/2018
	//*********************************************************
	//console.log(Value);
  //console.log("Starting GetShareData"); //TESTING POINT 1
	var e = document.getElementById("Currency")
	var strUser = e.options[e.selectedIndex].value;
  var XHR=createXHR();
	XHR.open("Get","PHP/ShareJson.php", true); // File path to new address when adding to the server
	//console.log("Waiting for ready state change"); //TESTING POINT 2
  XHR.onreadystatechange = (function() {
    console.log("Test2");

		if(XHR.readyState==4) {
			if(XHR.status==200) {
				var responsedata=JSON.parse(XHR.responseText);
				for(i=0;i < responsedata.length;i++){
					var Money = fx(responsedata[i].CurPrice).convert({ rom:"GBP", to: strUser });

					var newarticle=document.getElementById(responsedata[i].Sym);
						newarticle.innerHTML="";
					var options=document.createElement("p"); // Cretes a new paragraph element
						//options.innerHTML= "Trading ID: "+responsedata[i].Sym+"<br/>Name :"+responsedata[i].Name+"<br/>Current Price: "+responsedata[i].CurPrice+"<br/>Change : "+responsedata[i].Chg; // REmoved in v1.01
						options.innerHTML= "Trading ID: "+responsedata[i].Sym+"<br/>Name :"+responsedata[i].Name+"<br/>Current Price: "+strUser+" "+parseFloat(Money).toFixed(2);+"<br/>Change : "+responsedata[i].Chg; // Added V1.01

						newarticle.appendChild(options);
					//console.log("Changing Sharedata"); //TESTING POINT 3
				}
				var d = new Date();
				document.getElementById("Updated").innerHTML = d;
			}else if(XHR.status>=400) {
				alert("Could not request data");
			}
		}
	});
  XHR.send();
}

function mkShareData() {
	//*********************************************************
	// Gets the data from the json and creates the various share elements
	// Ian Holderness
	// V1.01 - 14/02/2018
	// Tested - 14/02/2018
	//*********************************************************
	var e = document.getElementById("Currency")
	var strUser = e.options[e.selectedIndex].value;
  //console.log("Starting GetShareData"); //TESTING POINT 1
  var XHR=createXHR();
	XHR.open("Get","PHP/ShareJson.php", true);
	//console.log("Waiting for ready state change"); //TESTING POINT 2
  XHR.onreadystatechange = (function() {
    //console.log("Test2"); //TESTING POINT 3

		if(XHR.readyState==4) {
			if(XHR.status==200) {
				//console.log("Got Sharedata"); //TESTING POINT 4
				var responsedata=JSON.parse(XHR.responseText);
				var main=document.getElementById("FsteScroll");
				for(i=0;i < responsedata.length;i++){
					// Passes the value to the fx function to convert from GBP to the users selected currency

					var Money = fx(responsedata[i].CurPrice).convert({ from:"GBP", to: strUser});
					var newarticle=document.createElement("article");
					//console.log("Creating Elements"); //TESTING POINT 5
					newarticle.setAttribute("id",responsedata[i].Sym);
					main.appendChild(newarticle);
					var options=document.createElement("p");
					//console.log("Creating Elements 2"); //TESTING POINT 6
					options.innerHTML= "Trading ID: "+responsedata[i].Sym+"<br/>Name :"+responsedata[i].Name+"<br/>Current Price: "+strUser+" "+parseFloat(Money).toFixed(2);+"<br/>Change : "+responsedata[i].Chg; // Updated V1.01
					newarticle.appendChild(options);
				}
				var d = new Date();
				document.getElementById("Updated").innerHTML = d;
			}else if(XHR.status>=400) {
				alert("Could not request data");
			}
		}
	});
  XHR.send();
}
