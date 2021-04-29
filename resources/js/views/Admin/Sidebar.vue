<template>
    <nav id="sidebar" class="bg-primary" v-bind:class="{ active : isActive }">
        <div class="sidebar-header">
            <h5>{{ title }}</h5>
        </div>

        <ul class="list-unstyled components">
            <li>
                <router-link to="/admin" :class="$route.name == 'Dashboard | Administrator' ? 'active' : ''">
                    <ph-house-line class="phospor"/> Dashboard
                </router-link>
            </li>
            <li>
                <router-link
                    to="/informasi"
                    :class="$route.name == 'Informasi | Administrator' ? 'active' : ''">
                    <ph-article class="phospor"/> Informasi
                </router-link>
            </li>
            <li>
                <router-link to="/personil" :class="$route.name == 'Personil | Administrator' ? 'active' : ''">
                    <ph-user-circle class="phospor"/> Personil
                </router-link>
            </li>
            <li>
                <router-link to="/kesatuan" :class="$route.name == 'Kesatuan | Administrator' ? 'active' : ''">
                    <ph-circles-three class="phospor"/> Kesatuan
                </router-link>
            </li>
            <li v-if="peneranganSatuan == '1'">
                <router-link
                    to="/penerangan-satuan"
                    :class="$route.name == 'PeneranganSatuan | Administrator' ? 'active' : ''">
                    <ph-lightbulb class="phospor"/> Penerangan Satuan
                </router-link>
            </li>
            <li>
                <router-link
                    to="/masyarakat"
                    :class="$route.name == 'Masyarakat | Administrator' ? 'active' : ''">
                    <ph-users class="phospor"/> Masyarakat
                </router-link>
            </li>
            <li v-if="hasEdisposisi == 1">
                <router-link
                    to="/edisposisi"
                    :class="$route.name == 'e-Disposisi | Administrator' ? 'active' : ''">
                    <ph-newspaper-clipping class="phospor"/> e-Disposisi
                </router-link>
            </li>
            <li>
                <router-link
                    to="/handy-talky"
                    :class="$route.name == 'HandyTalky | Administrator' ? 'active' : ''">
                    <ph-microphone class="phospor"/> HT Channel
                </router-link>
            </li>
            <li>
                <a
                    href="#laporan"
                    data-toggle="collapse"
                    aria-expanded="false"
                    class="dropdown-toggle">
                    <ph-book class="phospor"/> Laporan
                </a>
                <ul class="collapse list-unstyled" id="laporan">
                    <li>
                        <router-link to="/giat-rutin" :class="$route.name == 'Giat | Administrator' ? 'active' : ''">Giat Rutin</router-link>
                    </li>
                    <li>
                        <router-link to="/k2yd" :class="$route.name == 'K2yd | Administrator' ? 'active' : ''">K2YD</router-link>
                    </li>
                    <li>
                        <router-link to="/kunjungan" :class="$route.name == 'Kunjungan | Administrator'? 'active' : ''">Kunjungan</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/cegah-permasalahan-desa"
                            :class="$route.name == 'Cegah Permasalahan Desa | Administrator'? 'active' : ''">Cegah Permasalahan Desa</router-link>
                    </li>
                    <li>
                        <router-link to="/Sitkam" :class="$route.name == 'Sitkam | Administrator' ? 'active' : ''">Giat Langkah Sitkam</router-link>
                    </li>
                    <li>
                        <router-link to="/Satgas" :class="$route.name == 'Satgas | Administrator' ? 'active' : ''">
                            <span v-if="kegiatanBhabin == '0'">Satgas Merkuri</span>
                            <span v-else>Satgas Pangan</span>
                        </router-link>
                    </li>
                    <li v-if="danaDesa == '1'">
                        <router-link
                            to="/dana-desa"
                            :class="$route.name == 'Dana Desa | Administrator' ? 'active' : ''">Dana Desa</router-link>
                    </li>
                    <li v-if="danaDesa == '1'">
                        <router-link
                            to="/dana-sosial"
                            :class="$route.name == 'Dana Sosial | Administrator' ? 'active' : ''">Dana Sosial</router-link>
                    </li>
                </ul>
            </li>
            <li v-if="kegiatanBhabin == '1'">
                <a
                    href="#bhabin"
                    data-toggle="collapse"
                    aria-expanded="false"
                    class="dropdown-toggle">
                    <ph-book-open class="phospor"/> Laporan Bhabin
                </a>
                <ul class="collapse list-unstyled" id="bhabin">
                    <li>
                        <router-link to="/sambang" :class="$route.name == 'Sambang | Administrator' ? 'active' : ''">DDS / Sambang</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/problem-solving"
                            :class="$route.name == 'Problem Solving | Administrator' ? 'active' : ''">Problem Solving</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/pertolongan-pelayanan"
                            :class="$route.name == 'Pertolongan Pelayanan | Administrator' ? 'active' : ''">Pertolongan & Pelayanan</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/kegiatan-desa"
                            :class="$route.name == 'Kegiatan Desa | Administrator' ? 'active' : ''">Kegiatan Desa</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/pembinaan-kpm"
                            :class="$route.name == 'Pembinaan KPM/Pokdarkamtibmas | Administrator' ? 'active' : ''">Pembinaan Pokdarkamtibmas</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/deteksi-dini-produk-li"
                            :class="$route.name == 'Deteksi Dini Produk LI | Administrator' ? 'active' : ''">Deteksi Dini Produk LI</router-link>
                    </li>
                    <li>
                        <router-link
                            to="/pembinaan-siskamling"
                            :class="$route.name == 'Pembinaan Siskamling | Administrator' ? 'active' : ''">Pembinaan Siskamling</router-link>
                    </li>
                </ul>
            </li>
            <li>
                <router-link
                    to="/pengaturan"
                    :class="$route.name == 'Pengaturan | Administrator' ? 'active' : ''">
                    <ph-gear-six class="phospor"/> Pengaturan
                </router-link>
            </li>
            <li>
                <router-link to="/absensi" :class="$route.name == 'Absensi | Administrator' ? 'active' : ''">
                    <ph-book-bookmark class="phospor"/> Absensi Personil
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "sidebar",
        data() {
            return {
                title: appName,
                isActive: false,
                danaDesa: danaDesa,
                kegiatanBhabin: kegiatanBhabin,
                peneranganSatuan: peneranganSatuan,
                hasEdisposisi: hasEdisposisi
            };
        },
        methods: {
            toggleSidebar() {
                this.isActive = !this.isActive;
            }
        }
    };
</script>