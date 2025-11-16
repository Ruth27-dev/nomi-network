function toObject(text, locale) {
    // convert text to object
    let obj = JSON.parse(text);

    // return object
    return obj[locale];
}
window.toObject = toObject;

function getItemByLang(item, locale, arrayLang) {
    let value = toObject(item, locale);
    if (value && value !== "") {
        return value;
    }

    for (let lang of Object.keys(arrayLang)) {
        value = toObject(item, lang);
        if (value && value !== "") {
            return value;
        }
    }

    return "-";
}
window.getItemByLang = getItemByLang;

function displayAmountOfMoney(number = 0) {
    number = parseFloat(number);

    number = isNaN(number) ? 0 : number;

    return number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

window.displayAmountOfMoney = displayAmountOfMoney;

function dateRangePickerInputFormat() {
    return "DD/MM/YYYY";
}
window.dateRangePickerInputFormat = dateRangePickerInputFormat;

function dateFormat(date) {
    return moment(date).format("DD/MM/YYYY");
}
window.dateFormat = dateFormat;

function dateTimeFormat(date) {
    return moment(date).format("DD/MM/YYYY HH:mm A");
}
window.dateTimeFormat = dateTimeFormat;
function dateFormatSort(date) {
    return moment(date).format("DD-MMM-YYYY");
}
window.dateFormatSort = dateFormatSort;
async function getExchangeRate() {
    try {
        const response = await Axios({
            url: window.routes.fetchExchangeRateUrl,
            method: "GET",
        });

        return response?.data?.exchange_rate;
    } catch (e) {
        return null;
    }
}
window.getExchangeRate = getExchangeRate;

function calcOrderTotalPrice(data) {
    const percentage = appConfig.discount.type.percentage;
    const amount_type = appConfig.discount.type.amount;
    let total = {
        total_price: data?.total_price,
        total_discount: 0,
        details: {},
    };
    if(data) {
        if(data.discount_usage) {
            let {type, amount} = data.discount_usage.discount_data;
            if(type == percentage) total.total_discount = (data.total_price * amount) / 100;
            if(type == amount_type) total.total_discount = amount;

            total.total_price = data.total_price - total.total_discount;
        }
        else{
            data.details.forEach((detail) => {
                if(detail.discount_usage) {
                    let {type, amount, is_flat_discount} = detail.discount_usage.discount_data;
                    let total_price = is_flat_discount ? detail.unit_price : (detail.unit_price * detail.quantity);
                    let total_discount = 0;
                    if(type == percentage) total_discount = (total_price * amount) / 100;
                    if(type == amount_type) total_discount = amount;

                    total.details[detail.id] = {
                        total_price: is_flat_discount ? ((total_price * detail.quantity) - total_discount) : (total_price - total_discount),
                        total_discount: total_discount,
                    };
                    total.total_discount += total_discount;
                }
            });
            total.total_price = data?.total_price - total.total_discount;
        }
    }
    return total;
}
window.calcOrderTotalPrice = calcOrderTotalPrice;

function goTo(url){
    window.open(url, '_blank')
}
window.goTo = goTo;
