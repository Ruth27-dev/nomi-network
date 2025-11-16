<template x-data="{}" x-if="$store.autocomplete_{{ $name }}.active">
    <div class="autocomplete" x-data="autocomplete_{{ $name }}"
        :style="{
            zIndex: $store.libs.getLastIndex() + 1,
            top: `${options.top}px`,
            left: `${options.left}px`,
            width: `${options.width}px`
        }">
        <div class="autocomplete-body">
            {{ $slot }}
        </div>
        <script type="module">
            Alpine.data('autocomplete_{{ $name }}', () => ({
                options: null,
                init() {
                    this.options = Alpine.store('autocomplete_{{ $name }}').options;
                    const {
                        top,
                        left,
                        width,
                        height,
                    } = Alpine.store('libs').getPosition(this.options.target);
                    this.options.top = top + height + 10;
                    this.options.left = left;
                    this.options.width = width;
                    Alpine.store('animate').enter(this.$root, (res) => {
                        feather.replace();
                    });

                    this.$store.autocomplete_{{ $name }}.close = (data) => {
                        Alpine
                            .store('animate')
                            .leave(this.$root, () => {
                                this.$store.autocomplete_{{ $name }}.active = false;
                                this.$store.autocomplete_{{ $name }}.options.afterClose(data);
                            });
                    };
                }
            }));
        </script>
    </div>
</template>
<script type="module">
    Alpine.store('autocomplete_{{ $name }}', {
        active: false,
        close: () => {},
        options: {
            top: 0,
            left: 0,
            afterClose: () => {},
        }
    });
</script>
