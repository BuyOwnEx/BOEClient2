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

const calculateMargin = computed(() => {
    return `translateY(${props.itemIndex * 25 + 'px' })`;
});
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

    const percent = (props.volume / volumeDepth) * 100 + '%';
    const tablePadding = '4px';
    return `calc(100% - ${percent} - ${tablePadding})`;
});

</script>
<template>
    <span>
        <span
            v-if="type === 'bid'"
            class="orders-wall--bid"
            :style="{ transform: calculateMargin, left: calculateLength }"
        />
    </span>
</template>
<style scoped lang="sass">
.orders-wall-main
    position: absolute
    top: var(--bid-ask-header-height)
    bottom: 0
    height: var(--table-row-height)

.orders-wall--bid
    @extend .orders-wall-main
    background: rgba(146, 245, 150, 0.2)
    right: 0
    left: 0

.orders-wall--ask
    @extend .orders-wall-main
    background: rgba(245, 146, 146, 0.2)
    left: 0
    right: 0
</style>
