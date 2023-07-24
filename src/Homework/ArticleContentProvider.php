<?php

namespace App\Homework;

use App\Service\MarkdownParser;
use App\Twig\AppRuntime;

class ArticleContentProvider implements ArticleContentProviderInterface
{
    private $firstContent = <<<EOF
~~Sed ut perspiciatis~~, ***unde*** omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, sunt in [culpa qui officia](/) deserunt 
mollit anim id est laborum.

[Temporibus](/) autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet, consectetur adipiscing elit, facere possimus, ***omnis*** ~~voluptas assumenda est,...~~
EOF;

    private $secondContent = <<<EOF
~~Ut enim ad~~ ***minima*** veniam, quia voluptas sit, aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos, qui ratione voluptatem [sequi nesciunt](/), neque porro quisquam est, velit esse cillum dolore eu fugiat nulla pariatur.

At vero eos et accusamus et iusto odio dignissimos ducimus, quia voluptas sit, aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos, qui ratione [voluptatem sequi nesciunt](/), neque porro quisquam est, facere possimus, omnis voluptas assumenda est, omnis dolor repellendus?

[Sed](/) ut perspiciatis, nam libero tempore, cum soluta nobis est eligendi optio, cumque nihil impedit, quo minus id, ***quod*** ~~maxime placeat, quis...~~
EOF;

    private $thirdContent = <<<EOF
~~Равным образом дальнейшее~~ ***развитие*** различных форм деятельности требует определения и уточнения существующих финансовых и административных условий. Равным образом социально-экономическое развитие обеспечивает широкому кругу специалистов участие в формировании системы масштабного изменения ряда параметров. Таким образом, начало повседневной работы по формированию позиции способствует повышению актуальности существующих финансовых и административных условий? Таким образом, [новая модель](/) организационной деятельности обеспечивает широкому кругу специалистов участие в формировании новых предложений?

Таким образом, рамки и место обучения кадров играет важную роль в формировании соответствующих условий активизации! Разнообразный и богатый опыт выбранный нами инновационный путь представляет собой интересный эксперимент проверки форм воздействия. Равным образом консультация с профессионалами из IT обеспечивает актуальность направлений прогрессивного развития? Таким образом, постоянное информационно-техническое обеспечение нашей деятельности влечет за собой [процесс внедрения](/) и модернизации дальнейших направлений развития проекта? Повседневная практика показывает, что консультация с профессионалами из IT требует от нас анализа системы масштабного изменения ряда параметров?

Задача организации, в особенности же ***социально-экономическое*** ~~развитие требует определения...~~
EOF;

    private $fourthContent = <<<EOF
~~Задача организации, в~~ ***особенности*** же постоянное информационно-техническое обеспечение нашей деятельности обеспечивает актуальность модели развития. Не следует, однако, забывать о том, что новая модель организационной деятельности обеспечивает актуальность ключевых компонентов планируемого обновления. С другой стороны рамки и [место обучения кадров](/) требует от нас анализа существующих финансовых и административных условий. Практический опыт ***показывает, что*** ~~реализация намеченного плана...~~
EOF;

    private $fifthContent = <<<EOF
~~Lorem ipsum dolor~~ ***sit amet***, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, qui dolorem eum fugiat, quo voluptas nulla pariatur! [Duis aute irure](/) dolor in reprehenderit in voluptate, quia voluptas sit, aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos, qui ratione voluptatem sequi nesciunt, neque porro quisquam est, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua! Quis autem vel eum iure reprehenderit, nam libero tempore, cum soluta nobis est eligendi optio, cumque nihil impedit, quo minus id, quod maxime placeat, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Et harum quidem rerum facilis est et expedita distinctio, consectetur adipiscing elit, obcaecati cupiditate non provident, similique sunt in culpa, qui officia deserunt mollitia animi, id est laborum et dolorum fuga! Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet, nam libero tempore, cum soluta nobis est eligendi optio, cumque nihil impedit, quo minus id, quod maxime placeat, qui dolorem ipsum, quia dolor sit, amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt, ut labore et dolore magnam aliquam quaerat voluptatem.

Nemo enim ipsam ***voluptatem***, ~~qui blanditiis praesentium...~~
EOF;

    /**
     * @var string
     */
    private $word_with_bold;

    public function __construct(MarkdownParser $markdownParser, $word_with_bold)
    {
        $this->word_with_bold = $word_with_bold;
    }

    public function get(int $paragraphs, string $word = null, int $wordsCount = 0)
    {
        $texts = [
            $this->firstContent,
            $this->secondContent,
            $this->thirdContent,
            $this->fourthContent,
            $this->fifthContent
        ];

        $textsContent = <<<EOF

EOF;

        for ( $i = 0; $i < $paragraphs; $i++) {
            $textsContent = $textsContent . $texts[ rand(0, 4) ] . "<p>";
        }

        $contentExplode = explode(' ', $textsContent);

        if ( $wordsCount != 0 ) {
            $randomIndexes = array_rand($contentExplode, $wordsCount);

            $markArticleEnv = $this->word_with_bold;
            $wordMarkdownParameter = '';
            if ( $markArticleEnv == 'bold' ) {
                $wordMarkdownParameter = "**" . $word . "**";
            } elseif ( $markArticleEnv == 'italic') {
                $wordMarkdownParameter = "*" . $word . "*";
            }

            foreach ($randomIndexes as $randomIndex) {
                array_splice($contentExplode, $randomIndex, 0, $wordMarkdownParameter);
            }
        }

        $textsContent = implode(' ', $contentExplode);

        return  $textsContent;
    }
}