<template>
    <div :class="`rightbar-outline ${openMenu}`">
        <ul :class="`rightbar ${hotspotClass('expand')}`">
            <button class="btn e-btn e-btn-primary rightbar-close" @click="toggleMenu">
                <ph-caret-right class="phospor"/>
            </button>
            <li class="rightbar-header">
                Kegiatan 
                <a href="javascript:void(0);" @click="$parent.triggerLogout()" v-b-tooltip.hover title="Logout"
                class="d-block float-right"><ph-sign-out class="phopsor"/></a>
            </li>
            <perfect-scrollbar class="h-100">
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>1 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>2 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>3 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>4 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>5 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>6 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>7 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>8 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>9 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>10 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            <li>
                <div class="rightbar-widget">
                    <b-img src="https://dummyimage.com/600x400/fff/000" class="w-100" alt=""/>
                    <div>Satlantas Polres Kutai Kartanegara</div>
                    <span>22 Januari 2019 09:08</span>
                    <p>11 Giat strong point depan pemkab kukar</p>
                </div>
            </li>
            </perfect-scrollbar>
        </ul>
        <div :class="`hotspot ${hotspotClass('active')}`">
            <b-row class="mt-3">
                <b-col cols="3">
                    <div class="e-alert e-alert-success w-100 text-center">10</div>
                </b-col>
                <b-col cols="8">C(%) ≤ 29%</b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col cols="3">
                    <div class="e-alert e-alert-warning w-100 text-center">9</div>
                </b-col>
                <b-col cols="8">30% ≤ C(%) ≤ 79%</b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col cols="3">
                    <div class="e-alert e-alert-danger w-100 text-center">8</div>
                </b-col>
                <b-col cols="8">C(%) ≥ 80%</b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
export default {
    name: 'right-bar',
    data () {
        return {
            openMenu: '',
            hotspot: false,
        }
    },
    methods: {
        toggleMenu () {
            this.openMenu = this.openMenu == 'close' ? '' : 'close'
        },
        hotspotClass (val) {
            return this.hotspot ? val : ''
        }
    }
}
</script>