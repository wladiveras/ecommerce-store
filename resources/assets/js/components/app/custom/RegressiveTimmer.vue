<template>
    <div v-html="timmer" style="font-size: 25px;font-weight: 200;"></div>
</template>
<script>
export default {
    props : ['to'],
    data() {
        return {
            interval : null,
            timmer : null
        }
    },
    async created() {
        this.startTimmer()
    },
    methods : {
        startTimmer() {
            let date = new Date(this.to).getTime()
            this.interval = setInterval(_ => {
                let now = new Date().getTime()
                let distance = date - now
                let days    = Math.floor(distance / (1000 * 60 * 60 * 24))
                days = !days ? '' : `${("0" + days).slice(-2)}d : `
                let hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
                hours = !hours ? '00h' : `${("0" + hours).slice(-2)}h`
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
                minutes = !minutes ? ' : 00m' : ` : ${("0" + minutes).slice(-2)}m`
                let seconds = Math.floor((distance % (1000 * 60)) / 1000)
                seconds = !seconds ? ' : 00s' : ` : ${("0" + seconds).slice(-2)}s`
                this.timmer = days+hours+minutes+seconds
                if (distance < 0) {
                    clearInterval(this.interval)
                    this.timmer = "FINALIZADO"
                }
            }, 1000)
        }
    }
}
</script>