/* Price -> UiPrice - Класс из старой панели */
class UiPrice {

    constructor() {}

    get_all(data){
        //{price, count, discount, nds_proc, nds_includes}
        const r = this.prepare_data(data);
        const result = r;
        result["total"] = this.get_total(r);
        result["nds"] = this.get_nds(r);
        result["discount_sum"] = this.get_discount_sum(r);
        for(const i in result){
            result[i] = Price.string_to_number(result[i]);
            switch(i){
                default:
                    result[i] = this.round_value(result[i]);
                break;
                case "nds_includes":
                case "discount_no_round":
                case "count":
                    //пропуск
                break;
            }
            
        }
        
        return result;
    }

    static string_to_number(value){
        // console.log(value);
        // console.log(typeof value);
        if(typeof value === "number" || typeof value === "boolean"){
            return value;
        }
        if(value === undefined || value === "undefined" || value === ""){
            return 0;
        }
        const new_value = Number(value.replace(/ /g, "").replace(/,/g, ".").replace(/	/g, "").replace("руб.", "").replace("%", "").replace("₽", "").replace(/\s+/g, ""));
        return new_value;
    }

    static priceSet(value, postfix = ' руб.'){
        return this.priceView(value, {postfix});
    }

    static priceView(value, request = {}){
        const postfix = request?.postfix ? request?.postfix.trim() : 'руб.';
        const postfix_space = request?.postfix_space ? request?.postfix_space : ` `;
        const postfix_value = postfix == `` ? `` : `${postfix_space}${postfix}`;
        const mark = request?.mark ? request?.mark : '.';
        const pre_value = String(value).replace(/,/g, '.');
        const new_value = (Number.prototype.toFixed.call(parseFloat(pre_value) || 0, 2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ')).replace(".", mark) + postfix_value;
        return new_value;
    }

    prepare_data(data){
        const field = ["price", "count", "discount", "discount_no_round", "nds_includes", "nds_proc", "discount_price"];
        for(const elem of field){
            if(!(elem in data)){
                switch(elem){
                    default:
                        data[elem] = 0;
                    break;
                    case "count":
                    case "nds_includes":
                        data[elem] = 1;
                    break;
                    case "discount_price":
                        data[elem] = this.get_discount_price(data);
                    break;
                    case "nds_proc":
                        data[elem] = 20;
                    break;
                    case "discount_no_round":
                        data[elem] = data["discount"];
                    break;
                }
            }
        }
        return data;
    }

    get_discount_price(data){
        const discount_price = data["price"] * ((100 - data["discount"]) / 100);
        return discount_price;
    }

    round_value(value){
        return Price.string_to_number(Math.round((value)*100)/100);
    }

    nds_includes_check(nds_includes){
        return nds_includes == 1;
    }

    get_discount_sum(data){
        return data["price"] * data["count"] - data["discount_price"] * data["count"];
    }

    get_nds(data){
        let nds_result = 0;
        const total = data["price"] * data["count"];
        const discount_sum = this.round_value(total * data["discount"] / 100);
        const total_discount = total - discount_sum;
        if(this.nds_includes_check(data["nds_includes"])){
            //вкл. НДС
            nds_result = total_discount * data["nds_proc"] / (data["nds_proc"] + 100);
        } else {
            //не вкл. НДС
            nds_result = total_discount * (data["nds_proc"] / 100);
        }
        return nds_result;
    }

    get_total(data){
        let total = 0;
        if(this.nds_includes_check(data["nds_includes"])){
            //вкл. НДС
            total = data["discount_price"] * data["count"];
        } else {
            //не вкл. НДС
            const nds = this.round_value(this.get_nds(data));
            total = (data["count"] * data["discount_price"]) + nds;
        }
        return total;
    }
}