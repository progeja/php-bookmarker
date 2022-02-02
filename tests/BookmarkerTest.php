<?php

namespace BMParser\Testing;

require_once __DIR__ . '/../vendor/autoload.php';

use BMParser\Bookmarker;
use PHPUnit\Framework\TestCase;

class BookmarkerTest extends TestCase
{
    private Bookmarker $bmCls;
    private bool       $loaded = false;

    private string $sample     = '';
    private string $sampleTidy = '';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->sample     = '<!DOCTYPE NETSCAPE-Bookmark-file-1>
<!-- This is an automatically generated file.
     It will be read and overwritten.
     DO NOT EDIT! -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>Bookmarks</TITLE>
<H1>Bookmarks</H1>
<DL><p>
    <DT><H3 ADD_DATE="1594444031" LAST_MODIFIED="1643095202" PERSONAL_TOOLBAR_FOLDER="true">JĆ¤rjehoidjariba</H3>
    <DL><p>
        <DT><H3 ADD_DATE="1594444114" LAST_MODIFIED="1641966093">Games</H3>
        <DL><p>
            <DT><H3 ADD_DATE="1594444114" LAST_MODIFIED="1641974920">MineCraft</H3>
            <DL><p>
                <DT><H3 ADD_DATE="1594444114" LAST_MODIFIED="1594444114">Abi</H3>
                <DL><p>
                    <DT><A HREF="https://www.digminecraft.com/index.php" ADD_DATE="1516276285" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACMElEQVQ4jZWTTWgTURSFz3uZpLZJE2ziTxtN2rSpYIpgtOAPbrpXcRMoSBbpxlWo4t4u3YiL4qIbFRQLuqmLUhE0tQFFJNYISdVUDE3SiU2aSOLkZzJvnovitEKm6l1dOPd9nHsPjwDAnci9G9hRnHOZM/LmQNkcDQQCDLuUMB25PzEduTvVTjR9U8YBPAag6gEoUVUCAEJeSXUu1a93RWvXjMnmDADIA8Ks3+8f2dWBRqqqWVpVHwEo2nmnoVBsCcxhmGD9psmedM9kqVSqtHWw3RIuimIrFou1FhcXG0JRfQIAbL/B73a7Lbor6AliLre1twpCKe38b4BK0LHVcIUxppuEoCc4xvpvAYBvr2vQdowOhEIhMRwON1OJZS3yhRfRmxpA9hrHbIPe2zZ4wSm/AMDawQSc7PbY3Ed752Yezp0bv3TesZp4PwUA6Wz+9dP5lw/+cMApvwwAhEGmJfblovX4sNVh3uw2d9kJIb1Dw559ANCUW3XGVAvnnAjWnDBrnpfWOOfapQkhTUppbvSqbzlfKFUA2IdcfWdPjBwZ/VGVNveYTAbtBsFgUALwvO2BhCvIrG9knAcdh1wu5ynBaDqdz+S/etzOvt8zuiloa4ErUq2x4vO6zxgosayu5RKUUi2VvwIAIJ0Rl0xGoxkAsuLGR7Ljb/wToFAqvwIARWGyJNVThG4DDPrPAOdhl/LuQzK+/r240Gg25Vjyc7xSkZ4pLbUcX0l9+inV3v4CGofjzGQ7ZRYAAAAASUVORK5CYII=">DigMinecraft</A>
                    <DT><A HREF="http://minecraft-ids.grahamedgecombe.com/" ADD_DATE="1516262337" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAADI0lEQVQ4jVWTzWtcVRjGn/Nxz71zZ+bOnZk0yUysg1IprUIXQVxl1yoIggSmf4CbUmjpyq66diFxUSqUduG6JHXXjSB0oaAo4gdqCGprNDLJdCZz7839Pufc4yJpou/ueXme9908P+BkyHAd7Lm4dvNS/9anq2vvP3hn7dJ7Z/vP9+tmyACQ4xAADNfBNi5DA8DK6qDXW2lfX+x1r/jdRkcIgdHedL/N/Hvj75I79z/4bHSYGbKNyxv6+NK51bneqytLN5ot5+oLp+e9IIrhWI7i3ELDqXHLJtj+Zxwhwd1Hn3x9e/uLdAQA5OLF5dbi2+wmfH1t8GLPE9pBnMbSEQ7XUhHDCBxmGWHbKilTq7IV0rCITGR//Muj4kM2tWb9hUXvYUO4NuWsVFTSMIvZQRzDtgQKojA5mEEwzhxhmWCcyN2t2P3+8ZOVn7/cvM/DLaLNBWd/gqT99MmYz/db8JYc47cahICgXWuiU29gun9gfnj8FH/9OuWWEiaZZbMwyjX3fUP01sxyuU30MwlJQaJMIRBjDM4vIIpSjH4KsfXNDpn+FsJ2OWBTgh1t+QaEB0EO6xUDd8TRzWzs/bgH71QLYRBi+nuM4M8cSgFKKjgVh9yVIHUOVrOBAOAv+Q563SaM5SLKCjjPAJUzdFFH8O0BitJAGANaaVQaqHs1NF2BNC8R5AAHgCRTqJIZBAPmux7cGkUUEyy1W0hHMzg2R2UYTFXBFhbiXMJUBj3fAS0KYkAh2626CZPchElmZCmNrirYNofgBIXUoFyYvJAmLaQRnBlKqcwJMdwSgpWl7oQmx8uLvpxmxvI9hzRcZXamMRilkFqBqoI0G3WTKKLKMhMgpCMqw9ibry0ZzVnFCC6AcrcoJSmLQhLKKOGChFFMGKVoNmpKSsmKPGe50hEl+OiP3ejz4yqvLg96urlwo4xnV7sN4RkAlBC1uTOB1oYfakRFoe7GeXZ7e3JUZQAYAmwDhzC9tTzoMbDrji2unD7ldb7a/BuFrPaTPL8XhOGdSYrR0U+Go8wJzsMTnM/Ouf133ziz9vqZ/trcnNv/j+9/OP8LJbt8heoDW1gAAAAASUVORK5CYII=">Minecraft ID List</A>
                    <DT><A HREF="https://minecraft.gamepedia.com/Minecraft_Wiki" ADD_DATE="1516262360" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAADAElEQVQ4jV2TvYtcVRyG39/5uvfO3Dsze2eWcYIsKliZxspOJmgRJYUg2mmzsE1KCwub7fwHjIWkE1uxSNIoZFAsrASLIJJ12d2QdXfn+87cz3POz0pY85Tvw1O+hEOIMcZicjixuMbd7z/t25n9IB31gJB++PLde7Prfnw4VpPDiafr4+c/HuzNj1Z3glY4zhf5LanlwEQK+aqcksBj7zExN6IH33z87el/De3f/+Rmfzd6bzvP36/L5q0kbUfeA2VWwlnviQhCCtE0Ftoo6JYqfO1/i9r6UTavHtKHX9wuOrtxaFoGgggAW+8YLCDZMqQWcNZDaekEEZraqWJTIl8VOP7jmVfP/jpndWas2zToDBPZG3WkiTRaSQRpJIQg1HmNfJnLi79n2K5yXl5kjfUs6rL5WbWP1vfi13Y/e7LN3eZpAVc2pEINFWoQM0CEq5MZnh9NwezR7kUcRQG545ncMeoX9UqSsFo35Mhwsypo3svRjTpYnc4gYoPnf16CiaEJaEqH7GpLkioakqZQhrGiUEsGY6RSmssp/NM1stMKO56BoUflJdymgNYS1daj3TYwSsAbggec2hQFp3ELpBw6SYAbaRvsgWVRINgQ8qwCVIjaEtIdASIBD0aVV7DSkgg00booMZ0tcH61xNl0iYYYtXXQsYEONYJAwmjGfJ1jW1YAe/QHfVRWQVUNc2QUj9IeLhdrdp6prht04wjL1RZl42CLGs55GK3RjVu83uQA5WyUZwXvUTXWzTdb100iuW6IlatBQiHoDOCvlmiFAdqBxqJwyCqL2nqXGoluy0B14lBIIdWmtKosM1gPu7YOgoRMEg9rGSVZSCmc5BpsWbVCHTTOYbraCPn6S73jYdo5g2A9L81wb9AOtBRCSUGjfswnFysQQ2wrK7TWIol0UTr3K4O+XmbT7/5/poODvez89zvbohxLKW9Z6wcnlysYhWnN+nEj9aRV/PPgpyeL0+sdHY7HCi/w1d13+rfffHX/7Zsv7+9/9Eb6oh8DCgD9C60CjfPtnrBkAAAAAElFTkSuQmCC">Official Minecraft Wiki ā€“ The ultimate resource for all things Minecraft</A>
                    <DT><A HREF="http://www.minecraftforum.net/" ADD_DATE="1516339955" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACwElEQVQ4jXWTO4gdZRiGn/8yc+Z2Jmcv7gZlFZMIggTBRhHESgStFCJiUgs2CqJNerHRxkpsJRYJpAhoIVjYWImwEt1ATtR4YuJZZ8/ZmTlzn/8fixQbNb7dBy8PDx+8gvvkzEWUrx8/Z1v9wTiMKE16vmz3Llx6DfPvrrj3OPvVqXjohzdDFZ/3tLfW94au7xBSMAi7KG36odDqswsvTbN/AN748tFHht6+rYfRW47wfVe5eCOPtmtRUmEHixSSsilphqI0ov1UaPnJFy//elMD6MG5EY5ipQYHMxiUkFhr6fqOKA5ZVQUDAwJJIOPAC9x3s3bxDqA1gIOntHCRSqHRWGup25ooiOiNQQpF13cEfoBWiizPOEhyBaABVrst/cM5vdsRhRFxGON7PnVTY6yhbhrWjk0A+H02I7mRUl27+08NUOznrApBM3R0Jy0chziKASiKgjg+xv48IbvdElQblFdTXOEeAZ4sTzA3S2Z2n2Rvwfp4k2R1SDgeIY3DbHfBnR8TrBmgWXC8C9gh4Fvu3AWEvcfJYYetdpNr8ynTegqhwNv20emEKu1QmWWyhFPBJvQW07dHBl1vaMoKgNPeY9TzlgM3Yzb/E1MJHmo9vNUaXuBgjCUaj1lmKwAkQJYXjMMQx3FI85wo8HiQDZ7unuBEonlg8JFCkeYrRq5DmuVMxsGRQRT6LNKM7Y01lNIIAUJYhIAoCmjqisEawmhM3XZYa6mq+sigbO3rcRyn+wdLqrqmaVuUkiA1bdfjOA7bm+v0bUNZVXTGZNd/u3UWQAF89/PNn56f7HxUx66rpHymqUpZNx2+N2KVHlLVNVlRoZQyyWH68S3/jxe//r7Z/c+YAN575dktK9XnrjQvbG2si1+m11FSDElWfZOvknNXrhbz/13jvXn/1eeeMtiLaXJbLP9anLm8l/5wv97fN/pVVJhZKk4AAAAASUVORK5CYII=">Minecraft Forum</A>
                    <DT><A HREF="https://minecraft.gamepedia.com/Ticking_area" ADD_DATE="1542965793" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACvUlEQVQ4jY2TPYtcZQCFn/e99773e2Yz+5ndzRq2SAzYxDTWcWslK/gPRAxaqYWdkDaFiAgpbC0ssj/AIqUSDBFBRWHHOJOd2eS6M3Pvzv2Y+/VaCBtBWDzdgcPhKc6Bc/Tel2/vffrgg73zMuLf5uvk7sq3X33/jt9135pPsxuGZSBNSVPV0OpHZaPv7394896+eP/krOD2N7c3Arf8KJsubi3yajcIXepaU8wL2rrFMAUISZGWOL6F23VpGtHvLfsHgz+O74o3Pn594XU8FVzwsX1FmZdIKRGmoCkbvI5DOsuxXAvdanSrSeOcuqj49btDzPFoIrtpyeFPTwm7LltXNzAsA7/rUOuapm4RQJmVDH8eE8/mJOMEQynSeXZHANx881XdT6asLS8RLrkAuKFD02pkq4kGE54PJijPRPkOjm/x7OGf7fAoMUyAy0mFJV28JylPXoaNiyscH0a4ax6Dx0c4HQVVTT7OSe2MnuFz0fEYkmACLFr0qr8ukpMBwQ/POO3l7EqTeR7TyQUqSenZiiQt6AiF7bTUygT4p8CxLTp2hbXW4UqwTt1oxtOEy5XFME4JQ59FZbC91eM0L6mFJs4KACRAnBeMoinD4wm/H/1F3lSgNcJT9EKXbuDgKc3TKD4bz/b21gsCCVzdWSfq+DyfnJLOF/QCj1EUU9QNg2hG27a4yqIbuoyjGClPXxAYUnIUzXCURHVXCXwbLaC3uYMhBMsdl0urF3ACj1nWIITAsQx9RpAtKi2kIcpyTpYtaAyToq7xvZKybhClQEoDRUNR1yjTICtLeUbwykvrnwe26M+0z+7mCkHgsLIUcGVnFTQ0TcsoSphnNWHgIE3RD13js/+c6YtPbi3/9svw3VE03w9954ZrW/zYH7PkOcQL/ciyw/u9S+rewcHDE/6P9q7v7r12bfPcO/8NQHUxdZxa9U0AAAAASUVORK5CYII=">Ticking area ā€“ Official Minecraft Wiki</A>
                </DL><p>';
        $this->sampleTidy =
            '<!DOCTYPE NETSCAPE-Bookmark-file-1><!-- This is an automatically generated file. It will be read and overwritten. DO NOT EDIT! --><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8"><TITLE>Bookmarks</TITLE><H1>Bookmarks</H1><DL><p><DT><H3 ADD_DATE="1594444031" LAST_MODIFIED="1643095202" PERSONAL_TOOLBAR_FOLDER="true">JĆ¤rjehoidjariba</H3><DL><p><DT><H3 ADD_DATE="1594444114" LAST_MODIFIED="1641966093">Games</H3><DL><p><DT><H3 ADD_DATE="1594444114" LAST_MODIFIED="1641974920">MineCraft</H3><DL><p><DT><H3 ADD_DATE="1594444114" LAST_MODIFIED="1594444114">Abi</H3><DL><p><DT><A HREF="https://www.digminecraft.com/index.php" ADD_DATE="1516276285" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACMElEQVQ4jZWTTWgTURSFz3uZpLZJE2ziTxtN2rSpYIpgtOAPbrpXcRMoSBbpxlWo4t4u3YiL4qIbFRQLuqmLUhE0tQFFJNYISdVUDE3SiU2aSOLkZzJvnovitEKm6l1dOPd9nHsPjwDAnci9G9hRnHOZM/LmQNkcDQQCDLuUMB25PzEduTvVTjR9U8YBPAag6gEoUVUCAEJeSXUu1a93RWvXjMnmDADIA8Ks3+8f2dWBRqqqWVpVHwEo2nmnoVBsCcxhmGD9psmedM9kqVSqtHWw3RIuimIrFou1FhcXG0JRfQIAbL/B73a7Lbor6AliLre1twpCKe38b4BK0LHVcIUxppuEoCc4xvpvAYBvr2vQdowOhEIhMRwON1OJZS3yhRfRmxpA9hrHbIPe2zZ4wSm/AMDawQSc7PbY3Ed752Yezp0bv3TesZp4PwUA6Wz+9dP5lw/+cMApvwwAhEGmJfblovX4sNVh3uw2d9kJIb1Dw559ANCUW3XGVAvnnAjWnDBrnpfWOOfapQkhTUppbvSqbzlfKFUA2IdcfWdPjBwZ/VGVNveYTAbtBsFgUALwvO2BhCvIrG9knAcdh1wu5ynBaDqdz+S/etzOvt8zuiloa4ErUq2x4vO6zxgosayu5RKUUi2VvwIAIJ0Rl0xGoxkAsuLGR7Ljb/wToFAqvwIARWGyJNVThG4DDPrPAOdhl/LuQzK+/r240Gg25Vjyc7xSkZ4pLbUcX0l9+inV3v4CGofjzGQ7ZRYAAAAASUVORK5CYII=">DigMinecraft</A><DT><A HREF="http://minecraft-ids.grahamedgecombe.com/" ADD_DATE="1516262337" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAADI0lEQVQ4jVWTzWtcVRjGn/Nxz71zZ+bOnZk0yUysg1IprUIXQVxl1yoIggSmf4CbUmjpyq66diFxUSqUduG6JHXXjSB0oaAo4gdqCGprNDLJdCZz7839Pufc4yJpou/ueXme9908P+BkyHAd7Lm4dvNS/9anq2vvP3hn7dJ7Z/vP9+tmyACQ4xAADNfBNi5DA8DK6qDXW2lfX+x1r/jdRkcIgdHedL/N/Hvj75I79z/4bHSYGbKNyxv6+NK51bneqytLN5ot5+oLp+e9IIrhWI7i3ELDqXHLJtj+Zxwhwd1Hn3x9e/uLdAQA5OLF5dbi2+wmfH1t8GLPE9pBnMbSEQ7XUhHDCBxmGWHbKilTq7IV0rCITGR//Muj4kM2tWb9hUXvYUO4NuWsVFTSMIvZQRzDtgQKojA5mEEwzhxhmWCcyN2t2P3+8ZOVn7/cvM/DLaLNBWd/gqT99MmYz/db8JYc47cahICgXWuiU29gun9gfnj8FH/9OuWWEiaZZbMwyjX3fUP01sxyuU30MwlJQaJMIRBjDM4vIIpSjH4KsfXNDpn+FsJ2OWBTgh1t+QaEB0EO6xUDd8TRzWzs/bgH71QLYRBi+nuM4M8cSgFKKjgVh9yVIHUOVrOBAOAv+Q563SaM5SLKCjjPAJUzdFFH8O0BitJAGANaaVQaqHs1NF2BNC8R5AAHgCRTqJIZBAPmux7cGkUUEyy1W0hHMzg2R2UYTFXBFhbiXMJUBj3fAS0KYkAh2626CZPchElmZCmNrirYNofgBIXUoFyYvJAmLaQRnBlKqcwJMdwSgpWl7oQmx8uLvpxmxvI9hzRcZXamMRilkFqBqoI0G3WTKKLKMhMgpCMqw9ibry0ZzVnFCC6AcrcoJSmLQhLKKOGChFFMGKVoNmpKSsmKPGe50hEl+OiP3ejz4yqvLg96urlwo4xnV7sN4RkAlBC1uTOB1oYfakRFoe7GeXZ7e3JUZQAYAmwDhzC9tTzoMbDrji2unD7ldb7a/BuFrPaTPL8XhOGdSYrR0U+Go8wJzsMTnM/Ouf133ziz9vqZ/trcnNv/j+9/OP8LJbt8heoDW1gAAAAASUVORK5CYII=">Minecraft ID List</A><DT><A HREF="https://minecraft.gamepedia.com/Minecraft_Wiki" ADD_DATE="1516262360" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAADAElEQVQ4jV2TvYtcVRyG39/5uvfO3Dsze2eWcYIsKliZxspOJmgRJYUg2mmzsE1KCwub7fwHjIWkE1uxSNIoZFAsrASLIJJ12d2QdXfn+87cz3POz0pY85Tvw1O+hEOIMcZicjixuMbd7z/t25n9IB31gJB++PLde7Prfnw4VpPDiafr4+c/HuzNj1Z3glY4zhf5LanlwEQK+aqcksBj7zExN6IH33z87el/De3f/+Rmfzd6bzvP36/L5q0kbUfeA2VWwlnviQhCCtE0Ftoo6JYqfO1/i9r6UTavHtKHX9wuOrtxaFoGgggAW+8YLCDZMqQWcNZDaekEEZraqWJTIl8VOP7jmVfP/jpndWas2zToDBPZG3WkiTRaSQRpJIQg1HmNfJnLi79n2K5yXl5kjfUs6rL5WbWP1vfi13Y/e7LN3eZpAVc2pEINFWoQM0CEq5MZnh9NwezR7kUcRQG545ncMeoX9UqSsFo35Mhwsypo3svRjTpYnc4gYoPnf16CiaEJaEqH7GpLkioakqZQhrGiUEsGY6RSmssp/NM1stMKO56BoUflJdymgNYS1daj3TYwSsAbggec2hQFp3ELpBw6SYAbaRvsgWVRINgQ8qwCVIjaEtIdASIBD0aVV7DSkgg00booMZ0tcH61xNl0iYYYtXXQsYEONYJAwmjGfJ1jW1YAe/QHfVRWQVUNc2QUj9IeLhdrdp6prht04wjL1RZl42CLGs55GK3RjVu83uQA5WyUZwXvUTXWzTdb100iuW6IlatBQiHoDOCvlmiFAdqBxqJwyCqL2nqXGoluy0B14lBIIdWmtKosM1gPu7YOgoRMEg9rGSVZSCmc5BpsWbVCHTTOYbraCPn6S73jYdo5g2A9L81wb9AOtBRCSUGjfswnFysQQ2wrK7TWIol0UTr3K4O+XmbT7/5/poODvez89zvbohxLKW9Z6wcnlysYhWnN+nEj9aRV/PPgpyeL0+sdHY7HCi/w1d13+rfffHX/7Zsv7+9/9Eb6oh8DCgD9C60CjfPtnrBkAAAAAElFTkSuQmCC">Official Minecraft Wiki ā€“ The ultimate resource for all things Minecraft</A><DT><A HREF="http://www.minecraftforum.net/" ADD_DATE="1516339955" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACwElEQVQ4jXWTO4gdZRiGn/8yc+Z2Jmcv7gZlFZMIggTBRhHESgStFCJiUgs2CqJNerHRxkpsJRYJpAhoIVjYWImwEt1ATtR4YuJZZ8/ZmTlzn/8fixQbNb7dBy8PDx+8gvvkzEWUrx8/Z1v9wTiMKE16vmz3Llx6DfPvrrj3OPvVqXjohzdDFZ/3tLfW94au7xBSMAi7KG36odDqswsvTbN/AN748tFHht6+rYfRW47wfVe5eCOPtmtRUmEHixSSsilphqI0ov1UaPnJFy//elMD6MG5EY5ipQYHMxiUkFhr6fqOKA5ZVQUDAwJJIOPAC9x3s3bxDqA1gIOntHCRSqHRWGup25ooiOiNQQpF13cEfoBWiizPOEhyBaABVrst/cM5vdsRhRFxGON7PnVTY6yhbhrWjk0A+H02I7mRUl27+08NUOznrApBM3R0Jy0chziKASiKgjg+xv48IbvdElQblFdTXOEeAZ4sTzA3S2Z2n2Rvwfp4k2R1SDgeIY3DbHfBnR8TrBmgWXC8C9gh4Fvu3AWEvcfJYYetdpNr8ynTegqhwNv20emEKu1QmWWyhFPBJvQW07dHBl1vaMoKgNPeY9TzlgM3Yzb/E1MJHmo9vNUaXuBgjCUaj1lmKwAkQJYXjMMQx3FI85wo8HiQDZ7unuBEonlg8JFCkeYrRq5DmuVMxsGRQRT6LNKM7Y01lNIIAUJYhIAoCmjqisEawmhM3XZYa6mq+sigbO3rcRyn+wdLqrqmaVuUkiA1bdfjOA7bm+v0bUNZVXTGZNd/u3UWQAF89/PNn56f7HxUx66rpHymqUpZNx2+N2KVHlLVNVlRoZQyyWH68S3/jxe//r7Z/c+YAN575dktK9XnrjQvbG2si1+m11FSDElWfZOvknNXrhbz/13jvXn/1eeeMtiLaXJbLP9anLm8l/5wv97fN/pVVJhZKk4AAAAASUVORK5CYII=">Minecraft Forum</A><DT><A HREF="https://minecraft.gamepedia.com/Ticking_area" ADD_DATE="1542965793" ICON="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACvUlEQVQ4jY2TPYtcZQCFn/e99773e2Yz+5ndzRq2SAzYxDTWcWslK/gPRAxaqYWdkDaFiAgpbC0ssj/AIqUSDBFBRWHHOJOd2eS6M3Pvzv2Y+/VaCBtBWDzdgcPhKc6Bc/Tel2/vffrgg73zMuLf5uvk7sq3X33/jt9135pPsxuGZSBNSVPV0OpHZaPv7394896+eP/krOD2N7c3Arf8KJsubi3yajcIXepaU8wL2rrFMAUISZGWOL6F23VpGtHvLfsHgz+O74o3Pn594XU8FVzwsX1FmZdIKRGmoCkbvI5DOsuxXAvdanSrSeOcuqj49btDzPFoIrtpyeFPTwm7LltXNzAsA7/rUOuapm4RQJmVDH8eE8/mJOMEQynSeXZHANx881XdT6asLS8RLrkAuKFD02pkq4kGE54PJijPRPkOjm/x7OGf7fAoMUyAy0mFJV28JylPXoaNiyscH0a4ax6Dx0c4HQVVTT7OSe2MnuFz0fEYkmACLFr0qr8ukpMBwQ/POO3l7EqTeR7TyQUqSenZiiQt6AiF7bTUygT4p8CxLTp2hbXW4UqwTt1oxtOEy5XFME4JQ59FZbC91eM0L6mFJs4KACRAnBeMoinD4wm/H/1F3lSgNcJT9EKXbuDgKc3TKD4bz/b21gsCCVzdWSfq+DyfnJLOF/QCj1EUU9QNg2hG27a4yqIbuoyjGClPXxAYUnIUzXCURHVXCXwbLaC3uYMhBMsdl0urF3ACj1nWIITAsQx9RpAtKi2kIcpyTpYtaAyToq7xvZKybhClQEoDRUNR1yjTICtLeUbwykvrnwe26M+0z+7mCkHgsLIUcGVnFTQ0TcsoSphnNWHgIE3RD13js/+c6YtPbi3/9svw3VE03w9954ZrW/zYH7PkOcQL/ciyw/u9S+rewcHDE/6P9q7v7r12bfPcO/8NQHUxdZxa9U0AAAAASUVORK5CYII=">Ticking area ā€“ Official Minecraft Wiki</A></DL><p>';
        $this->bmCls      = new Bookmarker();
        $this->bmCls->parse($this->sample);
        $this->loaded = true;
    }

    public function testGetBmContentRaw()
    {
        $this->assertEquals($this->sample, $this->bmCls->getBmContent());
    }

    public function testGetBmContentTidy()
    {
        $this->assertEquals($this->sampleTidy, $this->bmCls->getBmContent(true));
    }
}
