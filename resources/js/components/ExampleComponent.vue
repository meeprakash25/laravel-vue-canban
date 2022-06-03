<template>
    <div class="my-content">
        asdasd
        <h1>
            heading
        </h1>
    </div>
</template>

import draggable from "vuedraggable"

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style lang="scss">

.my-content{
    margin: 0 20px;
    h1 {
        text-align: right;
        font-size: 30px!important;
    }
}
</style>
