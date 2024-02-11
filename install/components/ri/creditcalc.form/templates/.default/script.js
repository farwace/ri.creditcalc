if(!('RI' in window)){
    window.RI = {};
}

if(!('VueComponents' in window.RI)){
    window.RI.VueComponents = {};
}

if(!('VueDirectives' in window.RI)){
    window.RI.VueDirectives = {};
}

RI.formattedPrice = function (x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
}
RI.declension = function (number, txt) {
    var cases = [2, 0, 1, 1, 1, 2];
    return txt[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
}

RI.VueDirectives.ClickOutside = {
    mounted(el, binding, vnode, prevVnode) {
        var clickOutsideHandler = (e) => {
            var selector = '.' + el.classList.value.split(' ').join('.');
            if(e.target.closest(selector)){
                return;
            }
            if(e.target.closest('#overlay, .modal')){
                return;
            }
            binding.value();
        }
        el.clickOutside = clickOutsideHandler;
        document.addEventListener('click', el.clickOutside);
    },
    beforeUnmount: function (el){
        document.removeEventListener('click', el.clickOutside);
    }
}

RI.VueDirectives.Mask = {
    mounted(el, binding, vnode, prevVnode) {
        $(el).mask(binding.value);
    },
    beforeUnmount: function (el){
        $(el).unmask();
    }
}



RI.VueComponents.ContrAgentForm = {
    template: '#creditcalc-contragent-form',
    props: ['suggestions', 'isLoading'],
    data: function (){
        return {
            query: '',
            debouncedQuery: '',
            debounceQueryTimeout: 0,
            isError: false,
            selectedSuggestion: undefined,
            canShowSuggestions: true,
        }
    },
    directives: {
        clickOutside: RI.VueDirectives.ClickOutside
    },
    methods: {
        tryToNextStep: function (){
            if(this.isLoading){
                return false;
            }
            if(this.debouncedQuery.length < 1){
                this.isError = true;
                return false;
            }
            if(!this.selectedSuggestion){
                this.isError = true;
                return false;
            }
            this.$emit('next-step');
        },
        updateQuery: function (){
            var self = this;
            clearTimeout(this.debounceQueryTimeout);
            this.canShowSuggestions = true;
            this.debounceQueryTimeout = setTimeout(function (){
                self.debouncedQuery = self.query;
            }, 300);
        },
        setSelectedSuggestion(suggestion){
            this.selectedSuggestion = suggestion;
            this.canShowSuggestions = false;
            this.query = suggestion.value;
        },
        hideSuggestions(){
            this.canShowSuggestions = false;
        }
    },
    watch: {
        debouncedQuery: function (neoVal){
            this.isError = false;
            this.$emit('query', neoVal);
        },
        selectedSuggestion: function (neoVal){
            this.isError = false;
            this.$emit('select-suggestion', neoVal);
        }
    }

}

RI.VueComponents.Popup = {
    template: '#creditcalc-popup-template',
    props: ['title', 'subtitle'],
    directives: {
        clickOutside: RI.VueDirectives.ClickOutside
    },
    methods: {
        close: function (){
            this.$emit('close');
        }
    }
}

RI.VueComponents.PhoneForm = {
    template: '#creditcalc-phone-form-template',
    props: ['isLoading'],
    data: function (){
        return {
            phone: '',
            phoneError: false,
        }
    },
    directives: {
        mask: RI.VueDirectives.Mask
    },
    methods: {
        trySubmit: function (){
            var phonePattern = /^\+7 \(\d{3}\) \d{3} \d{2}-\d{2}$/;
            if(phonePattern.test(this.phone)){
                this.$emit('submit', this.phone);
            }
            else{
                this.phoneError = true;
            }
        }
    },
    watch: {
        phone: function (){
            this.phoneError = false;
        }
    }

}

RI.VueComponents.TotalForm = {
    template: '#creditcalc-total-form-template',
    props: ['suggestion', 'isLoading', 'percent', 'summMin', 'summMax'],
    methods: {

    },
    computed: {
        summMinFormated: function (){
            if(this.summMin){
                return RI.formattedPrice(this.summMin);
            }
            return '';
        },
        summMaxFormated: function (){
            if(this.summMax){
                return RI.formattedPrice(this.summMax);
            }
            return '';
        },
        companyType: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.type){
                if(this.suggestion.data.type == 'INDIVIDUAL'){
                    return 'ИП'
                }
                if(this.suggestion.data.type == 'LEGAL'){
                    return 'компании'
                }

            }
            return '';
        },
        companyStatus: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.state && this.suggestion.data.state.status){
                if(this.suggestion.data.state.status== 'ACTIVE'){
                    return 'Работает';
                }
                if(this.suggestion.data.state.status== 'LIQUIDATING'){
                    return 'Ликвидируется';
                }
                if(this.suggestion.data.state.status== 'BANKRUPT'){
                    return 'Банкротство';
                }
                if(this.suggestion.data.state.status== 'LIQUIDATED'){
                    return 'Ликвидирована';
                }
            }
            return ''
        },
        registerDate: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.state && this.suggestion.data.state.registration_date){
                var date = new Date(this.suggestion.data.state.registration_date);
                var day = "0" + date.getDay();
                var month = "0" + date.getMonth();
                var year = date.getFullYear();
                return day.slice(-2) + '.' + month.slice(-2) + '.' + year;
            }
        },
        companyAddress: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.address && this.suggestion.data.address.value){
                return this.suggestion.data.address.value;
            }
            return '';
        },
        inn: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.inn){
                return this.suggestion.data.inn;
            }
            return '';
        },
        ogrn: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.ogrn){
                return this.suggestion.data.ogrn;
            }
            return '';
        },
        boss: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.management && this.suggestion.data.management.name){
                return this.suggestion.data.management.name;
            }
            return '';
        },
        bossPost: function (){
            if(this.suggestion && this.suggestion.data && this.suggestion.data.management && this.suggestion.data.management.post){
                return this.suggestion.data.management.post;
            }
            return '';
        },


    }
}

RI.VueComponents.Ranges = {
    template: '#creditcalc-ranges-template',
    props: ['summMin', 'summMax', 'summ', 'periodMin', 'periodMax', 'period', 'isPopup'],
    data: function (){
        return {
            currentSumm: 0,
            currentPeriod: 0,
            currentSummFormatted: 0,
            currentPeriodFormatted: 0,
        }
    },
    mounted: function (){
        this.$nextTick(function (){
            this.currentSumm = this.summ;
            this.currentPeriod = this.period;
            this.formatSumm();
            this.formatPeriod();
        })

    },
    watch: {
        currentSumm: function (neoVal){
            if(neoVal > 0){
                this.formatSumm(neoVal);
                this.$emit('changed-summ', neoVal);
            }
        },
        currentPeriod: function (neoVal){
            if(neoVal > 0){
                this.formatPeriod(neoVal);
                this.$emit('changed-period', neoVal);
            }
        }
    },
    methods: {
        formatSumm: function (summ = undefined){
            if(!summ){
                summ = this.summ;
            }
            this.currentSummFormatted = RI.formattedPrice(parseInt(summ)) + ' ₽';
        },
        formatPeriod: function (period = undefined){
            if(!period){
                period = this.period;
            }
            this.currentPeriodFormatted = period + ' ' + RI.declension(parseInt(period), ['год', 'года', 'лет']);
        },
        prepareSumm: function (e){
            var neoVal = e.target.value;
            if(!neoVal){
                this.formatSumm();
                return;
            }
            neoVal = parseInt(neoVal.replace(/\D/g, ""));
            if(isNaN(neoVal)){
                this.formatSumm();
                return;
            }
            if(neoVal > parseInt(this.summMax)){
                this.currentSumm = parseInt(this.summMax);
                return;
            }
            if(neoVal < parseInt(this.summMin)){
                this.currentSumm = parseInt(this.summMin);
                return;
            }
            this.currentSumm = neoVal;
        },
        checkInputSumm: function (e){
            if(isNaN(e.data)){
                this.formatSumm();
            }
        }
    }
}

RI.VueComponents.StartInfo = {
    template: '#creditcalc-start-info-template',
    props: ['percent', 'paymentPerMonth', 'isPopup'],
    computed: {
        paymentPerMonthFormatted: function (){
            return RI.formattedPrice(this.paymentPerMonth);
        }
    }
}


RI.CreditCalc = function (data){
    BX.Vue3.BitrixVue.createApp({
        components: {
            "creditcalc-ranges": RI.VueComponents.Ranges,
            "creditcalc-start-info": RI.VueComponents.StartInfo,
            "creditcalc-popup": RI.VueComponents.Popup,
            "creditcalc-contragent-form": RI.VueComponents.ContrAgentForm,
            "creditcalc-total-form": RI.VueComponents.TotalForm,
            "creditcalc-phone-form": RI.VueComponents.PhoneForm,
        },
        data: function (){
            return Object.assign({}, data, {
                summ: 0,
                period: 0,
                step: 1,
                isLoading: false,
                canClosePopup: false,
                suggestions: [],
                selectedSuggestion: {},
                isSuccess: false,
            });
        },
        mounted: function (){
            this.summ = parseInt(this.summDefault);
            this.period = parseInt(this.periodDefault);
        },
        computed: {
            paymentPerMonth: function (){
                var P = (parseInt(this.percent)) / 100 / 12;
                return Math.round(this.summ * (P + (P / ( Math.pow((1 + P), (this.period * 12) ) - 1))));
            },
            popupTitle: function (){
                if(this.step == 3){
                    return 'Доступен кредит без залог';
                }
                return 'Узнайте условия кредита';
            },
            popupSubtitle: function (){
                if(this.step == 3){
                    return 'По итогам проверки скорингового балла Вам доступен кредит для бизнеса';
                }
                return 'доступные вашему бизнесу';
            }
        },
        watch: {
            step: function (neoVal){
                var self = this;
                setTimeout(function (){
                    self.canClosePopup = neoVal > 1;
                }, 50);
            }
        },
        methods:{
            setSumm(neoSumm){
                this.summ = neoSumm;
            },
            setPeriod(neoPeriod){
                this.period = neoPeriod
            },
            secondStep: function (){
                this.step = 2;
            },
            closePopup: function (){
                if(this.canClosePopup){
                    this.step = 1;
                }
            },
            setCompany(val){
                this.selectedSuggestion = val;
            },
            findContragent(query){
                this.isLoading = true;
                var self = this;
                BX.ajax.runAction('ri:creditcalc.contragentscontroller.getContragent', {
                    data: {
                        query: query
                    }
                }).then(function (res){
                    if(res.data && res.data.suggestions){
                        self.suggestions = res.data.suggestions;
                    }
                    self.isLoading = false;

                }).catch(function (e){
                    self.isLoading = false;
                })
            },
            tryLastStep(){
                if(this.selectedSuggestion){
                    var self = this;
                    setTimeout(function (){
                        self.step = 3;
                    }, 10)
                }
            },
            submitForm: function (phone){
                var inn = '';
                if(this.selectedSuggestion.data && this.selectedSuggestion.data.inn){
                    inn = this.selectedSuggestion.data.inn;
                }
                var data = {
                    phone: phone,
                    period: this.period,
                    summ: this.summ,
                    company: this.selectedSuggestion.value,
                    inn: inn,
                };

                this.isLoading = true;
                var self = this;
                BX.ajax.runAction('ri:creditcalc.contragentscontroller.addRequest', {
                    data: data
                }).then(function (res){
                    self.isSuccess = true;
                    self.isLoading = false;

                }).catch(function (e){
                    self.isLoading = false;
                })
            }
        }
    }).mount('#' + data.elId);
}
