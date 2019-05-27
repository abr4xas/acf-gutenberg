@wrapper(['block' => $block])

    <div class="container">
        @if(isset($block->title) || isset($block->intro))
        <div class="row">
            <div class="col-md-5 offset-md-1">
                @isset( $block->title )
                <h2 class="featured-title">{{ $block->title }}</h2>
                @endisset
            </div>
            <div class="col-md-5">
                @isset( $block->intro )
                <p>{{ $block->intro }}</p>
                @endisset
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-10 offset-md-1">
                @isset( $block->form_shortcode )
                <?php echo do_shortcode($block->form_shortcode); ?>
                @endisset
            </div>
        </div>
    </div><!-- .container -->

@endwrapper
