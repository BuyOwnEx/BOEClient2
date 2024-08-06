<script setup>
import {computed} from "vue";
import { useStore } from 'vuex';

const props = defineProps({
    itemIndex: {
        type: Number,
        required: true,
    },
    volume: {
        type: [Number, String],
        required: true,
    },
    type: {
        validator(value) {
            return ['ask', 'bid'].indexOf(value) !== -1;
        },
    },
});
const store = useStore();

const bidVolumeDepth = computed(() => {
    return store.state.trading.bid_volume_depth;
});
const askVolumeDepth = computed(() => {
    return store.state.trading.ask_volume_depth;
});

const calculateLength = computed(() => {
    let volumeDepth = 0;
    if (props.type === 'bid') volumeDepth = bidVolumeDepth.value;
    else if (props.type === 'ask') volumeDepth = askVolumeDepth.value;
    let percent = '0%';
    let padding = 0;
    if(props.type === 'bid')
    {
        percent = (props.volume / volumeDepth) * 100 + '%';
        padding = '4px';
        return `translateX(calc(100% - ${percent} - ${padding}))`;
    }
    else {
        percent = -(100 - (props.volume / volumeDepth) * 100) + '%';
        padding = '4px';
        return `translateX(calc(${percent} + ${padding}))`;
    }

});

</script>
<template>
    <div
        :class="`orders-wall--` + type"
        :style="{ transform: calculateLength }"
    />
</template>
<style scoped lang="sass">
.orders-wall-main
    position: absolute
    bottom: 0
    height: 100%
    width: 100%

.orders-wall--bid
    @extend .orders-wall-main
    background: rgba(70, 200, 76, 0.32)
    right: 0
    left: 0

.v-theme--dark .orders-wall--bid
    background: rgba(75, 191, 80, 0.24)

.orders-wall--ask
    @extend .orders-wall-main
    background: rgba(245, 146, 146, 0.2)
    left: 0
    right: 0
</style>
