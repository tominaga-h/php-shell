# Tasks

## GENERAL

- [x] src/PhpShell/Command配下のコマンドをsymfony/DIで取得できるようにする
- [x] symfony/consoleを使って実装する
- [x] readline等の標準入力はクラスに書かず、bin/consoleに書くことでテスト回避
- [x] symfony/consoleのApplicationクラスをラップするクラスをsrc/PhpShellに追加
- [ ] コマンドの出力結果に改行がなかったら%を追加して改行、改行があればなにもしない
- [x] シェルを起動時にメッセージ表示（Shellクラスのメソッドを使用）
- [x] とりあえず動くまで実装
- [x] デフォルトコマンドを排除
- [x] リストコマンドを実装
- [ ] ~~やっぱりcreateでsetInputやりたいからラッパークラスを実装~~
- [x] UserInputじゃなくInputFactoryを作る
- [x] うえ矢印で過去のコマンドを選択できるように
- [x] プロンプトに現在のディレクトリを表示（プロンプトビルダーを実装）
- [x] cdコマンド実装
- [ ] pwdコマンド実装

## FEATURE (将来)

- [ ] プロンプトにgitのブランチとか出せるようにしたい

