<template>
    <div class="topbar-outline">
        <div class="topbar">
            <div class="topbar-widget topbar-widget-purple">
                <div class="topbar-widget-icon">
                    <ph-git-fork class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Total Kejadian</div>
                    <span>189</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-blue">
                <div class="topbar-widget-icon">
                    <ph-git-commit class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Belum Ditangani</div>
                    <span>50</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-ocean">
                <div class="topbar-widget-icon">
                    <ph-git-diff class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Proses Penanganan</div>
                    <span>50</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-aqua">
                <div class="topbar-widget-icon">
                    <ph-git-merge class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Selesai</div>
                    <span>89</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'top-bar',
}
</script>