function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function onClickButton(event) {
    event.preventDefault();
    var dataOrder,dataProducts;
    var labelsOrder,labelsProducts;
    url = this.href;
    var canvasElts = document.getElementsByClassName("js-chart");
   for (let i = 0; i < canvasElts.length; i++) {
        canvasElts[i].hidden =!canvasElts[i].hidden;
      
       
   } 
    if (canvasElts[0].hidden == false) {
        
        axios.get(url).then((response) => {
            console.log(response.data);
            dataOrder = response.data.dataOrder;
            labelsOrder = response.data.labelsOrder;
            dataProducts = response.data.dataProducts;
            labelsProducts = response.data.labelsProducts;
             
            loadChartJsDoughnut(dataProducts, labelsProducts);
            loadChartJsBarts(dataOrder,labelsOrder);
        });

    }

}
function loadChartJsDoughnut(data, labels) {
    
    new Chart(document.getElementById("doughnut-chart"), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Nombre de produits",
                    backgroundColor:["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
                    data: data
                }
            ]
        },
        options: {
            title: {
                display: true,
                text: 'Stats:Les 5 Produits les plus frequents'
            }
        }
    });
}

function loadChartJsBarts(data,labels){
    
    new Chart(document.getElementById("bar-chart"), {
        type: 'bar',
        data: {
          labels:labels,
          datasets: [
            {
              label: "Nombre de commande",
              backgroundColor: ["#3e95cd", "#3e95cd","#3e95cd","#3e95cd","#3e95cd","#3e95cd", "#3e95cd","#3e95cd","#3e95cd","#3e95cd","#3e95cd","#3e95cd"],
              data:data
            }
          ]
        },
        options: {
          legend: { display: false },
          title: {
            display: true,
            text: "Stats:Nombre de commande mensuelle de l'année en cours"
          }
        }
    });
}


document.addEventListener("DOMContentLoaded", (event) => {

    document.getElementById("js-show").addEventListener('click', onClickButton);

});
