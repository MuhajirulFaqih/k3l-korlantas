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
                <router-link to="/personil" :class="$route.name == 'Personil | Administrator' ? 'active' : ''">
                    <ph-user-circle class="phospor"/> Personil
                </router-link>
            </li>
            <li>
                <router-link to="/kesatuan" :class="$route.name == 'Kesatuan | Administrator' ? 'active' : ''">
                    <ph-circles-three class="phospor"/> Kesatuan
                </router-link>
            </li>
            <li>
                <router-link
                    to="/masyarakat"
                    :class="$route.name == 'Masyarakat | Administrator' ? 'active' : ''">
                    <ph-users class="phospor"/> Masyarakat
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
                        <router-link to="/laporan-kegiatan" :class="$route.name == 'Laporan Kegiatan | Administrator' ? 'active' : ''">Kegiatan</router-link>
                    </li>
                    <li>
                        <router-link to="/laporan-quick-response" :class="$route.name == 'Laporan Quick Response | Administrator' ? 'active' : ''">Quick Response</router-link>
                    </li>
                    <li>
                        <router-link to="/laporan-kejadian" :class="$route.name == 'Laporan Kejadian | Administrator' ? 'active' : ''">Kejadian</router-link>
                    </li>
                    <li>
                        <router-link to="/laporan-absensi" :class="$route.name == 'Laporan Absensi | Administrator' ? 'active' : ''">Absensi</router-link>
                    </li>
                </ul>
            </li>
            <li>
                <router-link
                    to="/slider"
                    :class="$route.name == 'Slider | Administrator' ? 'active' : ''">
                    <ph-article class="phospor"/> Slider
                </router-link>
            </li>
            <li>
                <router-link
                    to="/pengaturan"
                    :class="$route.name == 'Pengaturan | Administrator' ? 'active' : ''">
                    <ph-gear-six class="phospor"/> Pengaturan
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
            };
        },
        methods: {
            toggleSidebar() {
                this.isActive = !this.isActive;
            }
        }
    };
</script>