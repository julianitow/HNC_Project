//https://www.creativebloq.com/how-to/create-a-slide-out-menu
function prepareMenu() {
  var Burger = document.getElementById('nav-icon1');
  var nodes = document.querySelectorAll('nav[data-action="expand"] *:first-child'); // Get this line again
  for(var i=0; i<nodes.length; i++){
    nodes[i].addEventListener("click",function(){ // Add burger menu element to open
    if(this.parentNode.className == "open"){
      this.parentNode.className = "";
      Burger.className="";
    }else{
      this.parentNode.className = "open";
      Burger.className="open";
    }
    });
  }
}
