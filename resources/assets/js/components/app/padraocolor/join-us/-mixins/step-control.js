export default {
    methods: {
        goBack() {
            this.$emit('move', -1)
        },
        proceed() {
            this.$refs.form.validate(v => {
                if (!v) return

                this.$emit('move')
            })
        },
        conclude() {
            this.$refs.form.validate(v => {
                if (!v) return

                this.$emit('finish')
            })
        }
    },
}