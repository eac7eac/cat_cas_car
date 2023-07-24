$(function () {

  $('[data-id=voteBlock]').each(function () {
    const $container = $(this);

    $container.on('click', '[data-id=voteButton]', function (e) {
      e.preventDefault();
      
      const href = $(this).data('href');
      
      $.ajax({
        url: href,
        method: 'POST'
      }).then(function (data) {
        const $voteCount = $container.find('[data-id=voteCount]');

        $voteCount.removeClass('text-success text-danger');
        if (data.votes > 0) {
          $voteCount.addClass('text-success');
        } else if(data.votes < 0) {
          $voteCount.addClass('text-danger');
        }
        
        $voteCount.text(data.votes);
      });
    });
  });

});