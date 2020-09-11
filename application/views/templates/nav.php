<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-md-center" id="navbar">
        <ul class="navbar-nav">
            <?php if (isset($noDropDown)) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url(); ?>">Home</a>
                </li>
            <?php else : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Charts</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown">
                        <?php foreach ($indicators as $ind) : ?>
                            <a class="dropdown-item" href="#" id="<?php echo $ind->code; ?>"><?php echo $ind->name; ?></a>
                        <?php endforeach; ?>
                    </div>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('app/ufcrud'); ?>">UF Crud</a>
            </li>
        </ul>
    </div>
</nav>