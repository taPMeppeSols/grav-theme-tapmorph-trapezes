# Shortcode examples

## Block: text
```
[trapezeBlock]
Aliquam finibus tristique enim vitae vestibulum. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis urna leo, malesuada molestie semper at, egestas eu elit. Suspendisse bibendum erat vitae nisi auctor, id lacinia diam pellentesque. Pellentesque blandit gravida magna, eu tristique odio faucibus vel. Nunc aliquam magna eros, eget dapibus velit molestie non. Nam nec erat volutpat sapien ornare vehicula in vitae diam. Pellentesque dignissim sem quis risus sodales, quis condimentum massa aliquet. Aliquam ultricies pellentesque leo, vitae convallis leo tristique vitae. Suspendisse pellentesque consequat arcu eget congue. Integer vel tellus a ipsum bibendum varius scelerisque et ipsum. Aenean aliquam, augue in efficitur pretium, eros justo dignissim ex, a tristique leo risus nec odio. Aliquam erat volutpat.
[/trapezeBlock]
```

## Block: Snippet
```
[trapezeBlock image='image_name.png']
<video controls>
	<source src="https://github.com/taPMeppeSols/grav-theme-tapmorph-trapezes/blob/master/images/demo-intro.mp4?raw=true" type="video/mp4">
	<source src="https://github.com/taPMeppeSols/grav-theme-tapmorph-trapezes/blob/master/images/demo-intro.ogg?raw=true" type="video/ogg">
	Your browser does not support the video tag.
</video>
[/trapezeBlock]
```

## Paragraph: Text only
```
[trapezeParagraph title="Text only"]
Nam scelerisque lorem at odio hendrerit tempor. Cras vitae fringilla tellus. Mauris venenatis mauris nisi. Vestibulum at enim et leo eleifend dapibus. Morbi nulla mi, porta sit amet odio vel, dignissim dapibus urna. Nulla in nulla eget turpis facilisis blandit. **Cras et orci rutrum massa varius maximus.** Sed pretium consectetur ex non tristique. Nullam vitae cursus nibh. Donec leo sem, iaculis nec vehicula et, mattis vel dui. Mauris nec felis luctus, egestas velit in, luctus leo. Pellentesque ac convallis augue. [u]Curabitur ex tellus, mollis non quam et, sagittis rutrum sapien.[/u] Donec et justo fermentum, elementum risus sit amet, suscipit ante. Proin fermentum efficitur massa et mattis.
[/trapezeParagraph]
```

## Paragraph with 1 anchor
```
[trapezeParagraph title="Paragraph with 1 anchor"]
Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce et ultrices ex, quis bibendum dolor. <b>Integer risus risus, sollicitudin quis dolor at, sollicitudin eleifend purus.</b> Aenean at convallis dui. Nam sed velit eleifend, sagittis erat id, pellentesque nisl. Mauris libero arcu, finibus et massa et, condimentum accumsan dolor. Proin ipsum nibh, ultrices vel leo id, fermentum viverra eros. Duis sit amet mattis libero, vitae finibus quam. Ut dui odio, consequat vitae sem sit amet, pulvinar tempor justo. Donec sit amet elementum nibh, vitae mollis tellus. Nunc non sem dictum, dictum nibh et, interdum neque. Vestibulum ante ipsum primis in faucibus orci **luctus et ultrices posuere cubilia curae**; Cras hendrerit sapien magna, sit amet volutpat metus hendrerit sed. Vivamus accumsan libero odio, sit amet aliquam lorem sagittis ut. _Nulla suscipit efficitur augue._ Lorem ipsum dolor sit amet, consectetur adipiscing elit.
[trapezeAnchors]
[trapezeAnchor href="https://tapmeppe.solutions" label="Homepage"/]
[/trapezeAnchors]
[/trapezeParagraph]
```

## Paragraph with more than 1 anchor
```
[trapezeParagraph title="Paragraph with more than 1 anchor"]
[u]Vestibulum ante ipsum primis in faucibus orci luctus et[/u] ultrices posuere cubilia curae; Fusce et ultrices ex, quis bibendum dolor. Integer risus risus, sollicitudin quis dolor at, sollicitudin eleifend purus. Aenean at convallis dui. Nam sed velit eleifend, sagittis erat id, pellentesque nisl. Mauris libero arcu, finibus et massa et, condimentum accumsan dolor. Proin ipsum nibh, ultrices vel leo id, fermentum viverra eros. <h5>Duis sit amet mattis libero, vitae finibus quam.</h5> Ut dui odio, consequat vitae sem sit amet, pulvinar tempor justo. **Donec sit amet elementum nibh, vitae mollis tellus.** Nunc non sem dictum, dictum nibh et, interdum neque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras hendrerit sapien magna, sit amet volutpat metus hendrerit sed. Vivamus accumsan libero odio, sit amet aliquam lorem sagittis ut. Nulla suscipit efficitur augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
[trapezeAnchors]
[trapezeAnchor href="."]Home[/trapezeAnchor]
[trapezeAnchor href="http://tapmeppe.solutions/help" label="Help"/]
[trapezeAnchor href="https://tapmeppe.solutions" label="Homepage"/]
[trapezeAnchor href="https://projects.tapmeppe.com" target="_self"]Projects[/trapezeAnchor]
[trapezeAnchor href="http://cloud.tapmeppe.com" label="Cloud" class="cloud"/]
[/trapezeAnchors]
[/trapezeParagraph]
```

## Carousel default
```
[trapezeCarousel]
[trapezeCarouselItem title="Text only"]
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lacinia felis et lacus feugiat, vitae egestas nunc porta. Cras imperdiet eleifend dui vel blandit. Curabitur et pharetra risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; **Aliquam magna quam, maximus in pharetra ut, eleifend finibus sapien.** Ut bibendum libero at bibendum laoreet. Nulla facilisi. Proin dictum, augue vel aliquet sagittis, nunc est vestibulum mauris, nec viverra eros felis quis nunc. Aliquam tincidunt felis justo, id pellentesque urna sollicitudin eu. Vestibulum sit amet efficitur mi. Aliquam tincidunt enim ac risus porttitor viverra. Nam volutpat mi et urna tincidunt congue. Nunc accumsan euismod nisl a ultricies. Curabitur maximus enim id elit ornare condimentum.
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text & image" image="image_name.png"]
Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce et ultrices ex, quis bibendum dolor. Integer risus risus, sollicitudin quis dolor at, sollicitudin eleifend purus. Aenean at convallis dui. Nam sed velit eleifend, sagittis erat id, pellentesque nisl. _Mauris libero arcu, finibus et massa et, condimentum accumsan dolor._ Proin ipsum nibh, ultrices vel leo id, fermentum viverra eros. Duis sit amet mattis libero, vitae finibus quam. Ut dui odio, consequat vitae sem sit amet, pulvinar tempor justo. Donec sit amet elementum nibh, vitae mollis tellus. Nunc non sem dictum, dictum nibh et, interdum neque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras hendrerit sapien magna, sit amet volutpat metus hendrerit sed. Vivamus accumsan libero odio, sit amet aliquam lorem sagittis ut. Nulla suscipit efficitur augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text, image & anchor" image="image_name.png"]
Donec sed lorem odio. Pellentesque vel sapien vel risus pellentesque egestas. Nulla non dignissim libero, in cursus metus. Quisque lacus nisi, rutrum a enim sit amet, hendrerit faucibus leo. Sed rhoncus, urna eget luctus viverra, urna mi iaculis tortor, eget pulvinar purus tortor vitae augue. Sed varius, sapien et pellentesque accumsan, enim sapien placerat elit, ac placerat arcu justo ut ipsum. <b>Nunc a sapien placerat, facilisis neque ut, tristique ante.</b> Suspendisse potenti. Nulla sed sem erat. Donec accumsan nec nisl at finibus. Etiam luctus placerat mauris, quis dapibus velit hendrerit sit amet. Morbi fringilla sapien quis erat finibus, a pharetra felis pharetra.
[trapezeAnchor href="https://tapmeppe.solutions" label="Theme creator"/]
[/trapezeCarouselItem]
[/trapezeCarousel]
```

## Carousel without indicators
```
[trapezeCarousel indicators=0]
[trapezeCarouselItem title="Text only"]
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lacinia felis et lacus feugiat, vitae egestas nunc porta. Cras imperdiet eleifend dui vel blandit. Curabitur et pharetra risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; **Aliquam magna quam, maximus in pharetra ut, eleifend finibus sapien.** Ut bibendum libero at bibendum laoreet. Nulla facilisi. Proin dictum, augue vel aliquet sagittis, nunc est vestibulum mauris, nec viverra eros felis quis nunc. Aliquam tincidunt felis justo, id pellentesque urna sollicitudin eu. Vestibulum sit amet efficitur mi. Aliquam tincidunt enim ac risus porttitor viverra. Nam volutpat mi et urna tincidunt congue. Nunc accumsan euismod nisl a ultricies. Curabitur maximus enim id elit ornare condimentum.
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text & image" image="image_name.png"]
Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce et ultrices ex, quis bibendum dolor. Integer risus risus, sollicitudin quis dolor at, sollicitudin eleifend purus. Aenean at convallis dui. Nam sed velit eleifend, sagittis erat id, pellentesque nisl. _Mauris libero arcu, finibus et massa et, condimentum accumsan dolor._ Proin ipsum nibh, ultrices vel leo id, fermentum viverra eros. Duis sit amet mattis libero, vitae finibus quam. Ut dui odio, consequat vitae sem sit amet, pulvinar tempor justo. Donec sit amet elementum nibh, vitae mollis tellus. Nunc non sem dictum, dictum nibh et, interdum neque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras hendrerit sapien magna, sit amet volutpat metus hendrerit sed. Vivamus accumsan libero odio, sit amet aliquam lorem sagittis ut. Nulla suscipit efficitur augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text, image & anchor" image="image_name.png"]
Donec sed lorem odio. Pellentesque vel sapien vel risus pellentesque egestas. Nulla non dignissim libero, in cursus metus. Quisque lacus nisi, rutrum a enim sit amet, hendrerit faucibus leo. Sed rhoncus, urna eget luctus viverra, urna mi iaculis tortor, eget pulvinar purus tortor vitae augue. Sed varius, sapien et pellentesque accumsan, enim sapien placerat elit, ac placerat arcu justo ut ipsum. <b>Nunc a sapien placerat, facilisis neque ut, tristique ante.</b> Suspendisse potenti. Nulla sed sem erat. Donec accumsan nec nisl at finibus. Etiam luctus placerat mauris, quis dapibus velit hendrerit sit amet. Morbi fringilla sapien quis erat finibus, a pharetra felis pharetra.
[trapezeAnchors]
[trapezeAnchor href="https://tapmeppe.solutions" label="Theme creator"/]
[/trapezeAnchors]
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text, image & anchor" image="image_name.png"/]
[/trapezeCarousel]
```

## Carousel without controls
```
[trapezeCarousel controls=0]
[trapezeCarouselItem title="Text only"]
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lacinia felis et lacus feugiat, vitae egestas nunc porta. Cras imperdiet eleifend dui vel blandit. Curabitur et pharetra risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; **Aliquam magna quam, maximus in pharetra ut, eleifend finibus sapien.** Ut bibendum libero at bibendum laoreet. Nulla facilisi. Proin dictum, augue vel aliquet sagittis, nunc est vestibulum mauris, nec viverra eros felis quis nunc. Aliquam tincidunt felis justo, id pellentesque urna sollicitudin eu. Vestibulum sit amet efficitur mi. Aliquam tincidunt enim ac risus porttitor viverra. Nam volutpat mi et urna tincidunt congue. Nunc accumsan euismod nisl a ultricies. Curabitur maximus enim id elit ornare condimentum.
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text & image" image="image_name.png"]
Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce et ultrices ex, quis bibendum dolor. Integer risus risus, sollicitudin quis dolor at, sollicitudin eleifend purus. Aenean at convallis dui. Nam sed velit eleifend, sagittis erat id, pellentesque nisl. _Mauris libero arcu, finibus et massa et, condimentum accumsan dolor._ Proin ipsum nibh, ultrices vel leo id, fermentum viverra eros. Duis sit amet mattis libero, vitae finibus quam. Ut dui odio, consequat vitae sem sit amet, pulvinar tempor justo. Donec sit amet elementum nibh, vitae mollis tellus. Nunc non sem dictum, dictum nibh et, interdum neque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras hendrerit sapien magna, sit amet volutpat metus hendrerit sed. Vivamus accumsan libero odio, sit amet aliquam lorem sagittis ut. Nulla suscipit efficitur augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text, image & anchor" image="image_name.png"]
Donec sed lorem odio. Pellentesque vel sapien vel risus pellentesque egestas. Nulla non dignissim libero, in cursus metus. Quisque lacus nisi, rutrum a enim sit amet, hendrerit faucibus leo. Sed rhoncus, urna eget luctus viverra, urna mi iaculis tortor, eget pulvinar purus tortor vitae augue. Sed varius, sapien et pellentesque accumsan, enim sapien placerat elit, ac placerat arcu justo ut ipsum. <b>Nunc a sapien placerat, facilisis neque ut, tristique ante.</b> Suspendisse potenti. Nulla sed sem erat. Donec accumsan nec nisl at finibus. Etiam luctus placerat mauris, quis dapibus velit hendrerit sit amet. Morbi fringilla sapien quis erat finibus, a pharetra felis pharetra.
[trapezeAnchors]
[trapezeAnchor href="https://tapmeppe.solutions" label="Theme creator"/]
[/trapezeAnchors]
[/trapezeCarouselItem]
[trapezeCarouselItem title="Text, image & anchor" image="image_name.png"/]
[/trapezeCarousel]
```

## Error (actually just to debug)
```
[trapezeError/]
```

---
### taPMeppe solutions (06.03.2022)
