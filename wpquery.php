<?php $my_query = new WP_Query('showposts=5'); while ($my_query->have_posts()) : $my_query->the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

     <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
    
     <div class="pagemeta"><?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?></div>
    
     <div class="entry">
      <?php if(get_the_post_thumbnail()) { ?> <div class="alignleft"><?php the_post_thumbnail('thumbnail'); ?></div> <?php } ?>
      <?php the_excerpt(); ?>
     </div>
    
     <div class="pagemeta">Posted in <?php the_category(', '); ?></div>
     
 </div> <!--post -->


 <?php endwhile;?>
