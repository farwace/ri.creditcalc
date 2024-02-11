/*
BX.ajax.runAction('ri:creditcalc.contragentscontroller.getContragent', {
    data: {
        query: 7707083893
    }
}).then(function (res){
    console.log('>>> RES', res);
}).catch(function (e){
    console.log('>>> ERROR ', e);
})*/


if(!('RI' in window)){
    window.RI = {};
}

if(!('VueComponents' in window.RI)){
    window.RI.VueComponents = {};
}

RI.VueComponents.Ranges = {
    template: '#creditcalc-ranges-template',
    props: ['summMin', 'summMax', 'summ', 'periodMin', 'periodMax', 'period'],
    data: function (){
        return {
            currentSumm: 0,
            currentPeriod: 0,
        }
    },
    mounted: function (){
        this.$nextTick(function (){
            this.currentSumm = this.summ;
            this.currentPeriod = this.period;
        })

    },
    watch: {
        currentSumm: function (neoVal){
            if(neoVal > 0){
                this.$emit('changed-summ', neoVal);
            }
        },
        currentPeriod: function (neoVal){
            if(neoVal > 0){
                this.$emit('changed-period', neoVal);
            }
        }
    }
}

RI.VueComponents.StartInfo = {
    template: '#creditcalc-start-info-template',
    props: ['percent', 'paymentPerMonth'],
    computed: {
        paymentPerMonthFormatted: function (){
            return this.paymentPerMonth.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }
    }
}


RI.CreditCalc = function (data){
    BX.Vue3.BitrixVue.createApp({
        components: {
            "creditcalc-ranges": RI.VueComponents.Ranges,
            "creditcalc-start-info": RI.VueComponents.StartInfo,
        },
        data: function (){
            return Object.assign({}, data, {
                summ: 0,
                period: 0,
                step: 1,
                isLoading: false,
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
            }
        }
    }).mount('#' + data.elId);
}