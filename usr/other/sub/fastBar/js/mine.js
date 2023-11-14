function mineGame(){
    const cell = {
        X: 8,
        Y: 9,
        size: 30,
        border: 1,
    };
    const mine = {
        level: {
            low: {
                count: 10
            }
        }
    }
    $(".contentBar").append(`<div id="minesweeper"></div>`);
    $("#minesweeper").append(`<div id="minesweeper_count_bomb">`+mine["level"]["low"]["count"]+`</div>`);
    $("#minesweeper").css({
        width: cell.X * cell.size + cell.X * cell.border * 2,
        height: cell.Y * cell.size + cell.Y * cell.border * 2,
        display: "flex",
        "flex-wrap": "wrap",
    });
    function appendCell(cell){
        for(let y=0;y<cell.Y;y++){
            for(let x=0;x<cell.X;x++){
                $("#minesweeper").append(`<div id="`+x+`_`+y+`" class="cell" data-x="`+x+`" data-y="`+y+`"></div>`);
            }
        }
        $(".cell").css({
            width: cell.size,
            height: cell.size,
        });
    }
    function mineSet(cell, mine, level){
        let count = 0;
        let arrayMine = {};
        while(count < mine["level"][level]["count"]){
            let x = Math.floor(Math.random() * cell.X);
            let y = Math.floor(Math.random() * cell.Y);
            if(x+'_'+y in arrayMine){
                continue;
            } else {
                count++;
                arrayMine[x+'_'+y] = {
                    X: x,
                    Y: y,
                };
                $("#"+x+'_'+y).data("value", "mine");
            }
        }
    }
    function numberMineSet(){
        $(".cell").each(function(i, elem){
            if($(elem).data("value") !== "mine"){
                let x = $(elem).data("x");
                let y = $(elem).data("y");
                let counter = 0;
                for(let i=0;i<9;i++){
                    let x_ = x -((Math.floor(i/3)-1));
                    let y_ = y - (((i%3)-1));
                    if($("#"+x_+"_"+y_).data("value") === "mine"){
                        counter++;
                    }
                }
                if(+counter !== 0){
                    $(elem).data("value", counter);
                }
            }
        });
    }
    function openRec(x, y, cell, mine, level){
        let elem = $("#"+x+"_"+y);
        if(elem.hasClass("cellOpen") || elem.hasClass("mineDis"))return;
        elem.addClass("cellOpen");
        let value = $(elem).data("value");
        switch(value){
            default:
                $(elem).html(value);
            break;
            case "mine":
                alert("You lose!");
                return startGame(cell, mine, "low");
            break;
        }
        if(elem.html() === ""){
            for(let ks=0;ks<9;ks++){
                openRec(x-((Math.floor(ks/3)-1)),y-(((ks%3)-1)), cell, mine, level);
            }
        }
        let cellOpen = $(".cellOpen").length;
        let cellX = cell.X;
        let cellY = cell.Y;
        let levelCount = mine["level"][level]["count"];
        if(cellOpen === cellX*cellY - levelCount){
            alert("You Win!");
            return startGame(cell, mine, "low");
        }
    }
    function startGame(cell, mine, level){
        $("#minesweeper").empty();
        appendCell(cell);
        mineSet(cell, mine, "low");
        numberMineSet();
    }
    startGame(cell, mine, "low");
    $(document).on("contextmenu", ".cell", function(e){
        e.preventDefault();
        if(!$(this).hasClass("cellOpen")){
            $(this).toggleClass("mineDis");
        }
    });
    $(document).on("click", ".cell", function(){
            let x = $(this).data('x');
            let y = $(this).data('y');
            openRec(x, y, cell, mine, "low");
    });
}