package html

import (
	"github.com/tdewolff/minify"
	"github.com/tdewolff/minify/html"
)

// Minify HTML doc
func Minify(sHTML string) string {
	m := minify.New()
	m.AddFunc("text/html", html.Minify)
	sHTML, err := m.String("text/html", sHTML)
	if err != nil {
		panic(err)
	}
	return sHTML
}
