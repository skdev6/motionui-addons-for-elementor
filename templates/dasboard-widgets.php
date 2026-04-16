<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">
    <ul class="th-das-navbar inline-nav">
        <li class="current-menu-item">
            <a href="#">
                All Widgets
            </a>
        </li>
        <li>
            <a href="#">
                Cards
            </a>
        </li>
        <li>
            <a href="#">
                Buttons
            </a>
        </li>
    </ul>
    <div class="right-menu-item d-flex gap-2 align-items-center">
        <div class="th-switch-control th-text-primary d-flex align-items-center">
            <input type="checkbox" id="Enable-All"/>
            <span class="switch-label"></span>
            <label for="Enable-All">Enable All</label>
        </div>
        <div class="btn-wrap">
            <a href="#" class="th-das-btn btn-sm">Save Change</a>
        </div>
    </div>
</div>
<div class="widget-card-wrap">
    <?php for ($i=0; $i < 60; $i++) { ?>
    <div class="th-widget-card">
        <div class="icon-wrap">
            <i class="th-icon">
                <svg width="59" height="14" viewBox="0 0 59 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M57.508 4.7859C57.3877 4.13509 57.1917 3.50275 56.9379 2.9139C56.6394 2.23234 56.2609 1.61317 55.8199 1.10342C55.6017 0.962804 55.3835 0.830974 55.1608 0.716721C55.1162 0.69431 55.0761 0.68596 55.0361 0.676732C54.8089 0.531718 54.5372 0.457014 54.27 0.329577C54.2522 0.325183 54.2433 0.325183 54.2299 0.320788C54.1141 0.254873 53.9939 0.197747 53.8647 0.158197C53.3703 0.0263664 52.8804 0.0175779 52.3726 0.0175779C52.3192 0.0175779 52.2791 0.0307596 52.2346 0.0443822C52.003 0.0175765 51.7669 0 51.5264 0H6.23506C3.93686 0 1.95044 1.33149 1.01513 3.25183C0.908236 3.2738 0.810251 3.33093 0.734535 3.44518C0.0887246 4.41633 -0.107245 5.56766 0.0530937 6.68822C0.0263705 6.74535 0.00855502 6.81082 0.00855502 6.89036C0.0214977 7.42733 0.0886374 7.96168 0.208979 8.48551C0.364864 8.56857 0.502934 8.69205 0.605373 8.86782C1.13984 9.77306 1.88809 10.4102 2.75214 10.858C3.30442 11.1481 3.90569 11.3594 4.52923 11.5172C5.37101 12.9981 6.9744 14 8.80939 14H30.5398C30.6912 13.9033 30.8872 13.8857 31.0386 14H54.1007C56.8042 14 59 11.8296 59 9.1662V8.25657C59 6.89519 58.4255 5.66477 57.508 4.7859ZM56.4257 6.62274C56.4257 9.28617 54.2255 11.4565 51.5264 11.4565H28.4598C28.3084 11.3423 28.1124 11.3599 27.961 11.4565H6.23506C5.55362 11.4565 4.8989 11.3159 4.30653 11.0654C3.65627 10.793 3.08172 10.3843 2.61407 9.87457L2.60961 9.86974C1.78825 8.98257 1.33375 7.82388 1.33581 6.6223V5.71267C1.33581 3.04573 3.53156 0.878873 6.23506 0.878873H51.5264C53.0096 0.878873 54.3413 1.53363 55.2365 2.56191C56.0041 3.43686 56.4263 4.55548 56.4257 5.71267V6.62274Z" fill="black"/></svg>
            </i>
        </div> 
        <div class="card-con">
            <h4 class="title">Advanced Button</h4>
            <div class="gap-2 d-flex align-items-center">
                    <a href="#" class="th-doc-link">
                        <i class="th-icon-link"></i>
                        Demo
                    </a>
                    <a href="#" class="th-doc-link">
                        <i class="th-icon-video"></i>
                        Tutorial
                    </a>
            </div>
        </div>
        <div class="th-switch-control d-flex align-items-center ml-auto">
            <input type="checkbox" id="toggle-btn-widget"/>
            <label class="switch-label" for="toggle-btn-widget"></label>
        </div>
    </div>
     <?php } ?>
</div>