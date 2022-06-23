(function () {
    Favourite = {
        init: function (param) {
            this.param = param;
            this.sendRequest();
            this.elements = document.querySelectorAll("." + param.elementClass);
            if(this.elements.length > 0){
                for(var i in this.elements){
                    BX.bind(this.elements[i], 'click', BX.delegate(this.sendRequest, this));
                }
            }

            BX.bind(BX(param.totalId), 'click', BX.delegate(this.sendRequest, this));

        },
        sendRequest: function (event) {
            var data = {
                parameters: this.param.parameters,
                template: this.param.template,
            };
            if(event !== undefined){
                var action = 'add';
                if(BX.hasClass(event.target, this.param.activeClass)){
                    action = 'delete';
                }
                BX.toggleClass(event.target, this.param.activeClass);
                data.action = action;
                data.productId = event.target.dataset.id;

                BX.ajax({
                    url: this.param.ajaxUrl,
                    data: data,
                    method: 'POST',
                    dataType: 'json',
                    timeout: 30,
                    onsuccess: BX.delegate(this.updateSuccess, this),
                    onfailure: e => {
                        console.error( e )
                    }
                })
            }else{
                BX.ajax({
                    url: this.param.ajaxUrl,
                    data: data,
                    method: 'POST',
                    dataType: 'json',
                    timeout: 30,
                    onsuccess: BX.delegate(this.updateSuccess, this),
                    onfailure: e => {
                        console.error( e )
                    }
                })
            }
        },
        updateSuccess: function (res) {

            if (BX(this.param.totalId)) {
                BX(this.param.totalId).innerHTML = res.COUNT_ITEMS;
            }
            let item;
            if(res.ITEMS.length > 0){
                for(let i in res.ITEMS){
                    item = document.querySelector('[data-id="'+res.ITEMS[i]+'"]');
                    BX.addClass(item, this.param.activeClass);
                }
            }
        }
    };
})();