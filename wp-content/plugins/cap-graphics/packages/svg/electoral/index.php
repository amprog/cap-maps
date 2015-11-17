<script>
    if (true) {
        try {
            $sf.ext.register(300, 250, status_update);
            features = $sf.ext.supports();
            console.log("creative loaded")
            console.log("safeframe enabled\n"  + JSON.stringify(features));
            win = $sf.ext.geom().win; //location, width, and height of the browser window boundaries relative to the device screen
            console.log("window size\n" + JSON.stringify(win));
        } catch (e) {
            console.log("no safeframe available: " + e.message);
        }
    }

    function dfpExpand(){
        exp = $sf.ext.geom().exp;// expected distance available for expansion within the browser window
        console.log("expandable area available\n" + JSON.stringify(exp));
        config = {t: exp.t, r: exp.r, b: exp.b, l: exp.l};
        $sf.ext.expand(config);
        containerAdjustment();
    }

    function dfpCollapse() {
        $sf.ext.collapse();

    }

    function status_update(status, data){
        if (status == "geom-update") {
            return;
        }
        console.log(status + "\n" + JSON.stringify(data));
        selfLocationInfo();
    }

    function containerAdjustment() {
        win = $sf.ext.geom().win;
        dfpContainer = document.getElementById("dfpContainer");
        dfpContainer.style.width = (win.l) + 'px';
        dfpContainer.style.height = (win.b) + 'px';
    }

    function selfLocationInfo(){
        self = $sf.ext.geom().self; // z-index and location, width, and height of the SafeFrame container relative to the browser window (win)
        console.log("updated creative location and info\n" + JSON.stringify(self));
    }

    dfpExpand();
</script>
<div id="dfpContainer" style="width:300px; height:250px; background-color: rgba(0, 0, 0, 0.2);">
    <button onclick="dfpExpand();">dfpExpand</button>
    <button onclick="dfpCollapse();">X</button>
</div>