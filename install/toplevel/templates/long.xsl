<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented one subject per page with one to several 
	 assessment grades and a long written comment and a categories' table; 
	 there is an optional cover page-->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">

  <xsl:if test="reports/coversheet='yes'">
	<div class="bighead">
	  <div id="logo">
		<img src="../images/schoollogo.png" width="140px"/>
	  </div>
	  <table class="studenthead">
		<tr>
		  <td>
			<label>
			  Student
			</label>
			<xsl:value-of select="displayfullname/value/text()" />
		  </td>
		</tr>
		<tr>
		  <td>
			<label>
			  Form
			</label>
			<xsl:value-of select="registrationgroup/value/text()" />
			<xsl:text>&#160;&#160;&#160;&#160;&#160;&#160;</xsl:text>
			<label>Date</label>
			<xsl:value-of select="reports/publishdate/text()" />
		  </td>
		</tr>
		<tr>
		  <td><label>Number of subjects in report</label>
		  <xsl:value-of select="count(reports/report)" />
		  </td>
		</tr>
	  </table>
	</div>

	<div class="spacer"></div>

	<xsl:apply-templates select="reports/summaries/summary" />

	<hr />
  </xsl:if>

  <xsl:apply-templates select="reports" />

</xsl:template>

<xsl:template match="reports/summaries/summary">
  <xsl:if test="description/type='com'">
	<div class="sumcomment">
	  <xsl:value-of select="description/value/text()" /><br />
	  <xsl:apply-templates select="comments"/>
	</div>
  </xsl:if>
  <xsl:if test="description/type='sig'">
	<div class="sumsignature">
	  <xsl:value-of select="description/value/text()" />
	  <p class='signature'>Signed</p>
	</div>
  </xsl:if>
  <xsl:if test="description/type='att'">
	<div class="sumsignature">
	  <xsl:value-of select="description/value/text()" />
	</div>
  </xsl:if>
</xsl:template>

<xsl:template match="reports">
  <xsl:variable name="assindex">
	<xsl:value-of select="count(asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name="ratindex">
	<xsl:value-of select="count(cattable/rat/name)+1"/>
  </xsl:variable>
  <xsl:apply-templates select="report">
	  <xsl:with-param name="assindex" select="$assindex"/>
	  <xsl:with-param name="ratindex" select="$ratindex"/>
  </xsl:apply-templates>
</xsl:template>

<xsl:template match="report">
  <xsl:param name="assindex">1</xsl:param>
  <xsl:param name="ratindex">1</xsl:param>
	<h1>
	  <label>
		Subject
	  </label>
	  <xsl:choose>
		<xsl:when test="component/value/text()!=' '">
		  <xsl:value-of select="component/value/text()" />
		</xsl:when>
		<xsl:otherwise>
		  <xsl:value-of select="subject/value/text()" />
		</xsl:otherwise>
	  </xsl:choose>
	</h1>

	<table class='studenthead'>
	  <tr>
		<td>
		  <label>
			Name
		  </label>
		<xsl:value-of select="../../forename/value/text()" /></td>
		<td>
		  <xsl:value-of select="../../surname/value/text()" />
		</td>
		<td id='form'>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="../../registrationgroup/value/text()" />
		</td>
	  </tr>
	</table>


	<div class='grades' id='gradesleft'>
	  Summer Term's Work
	  <table>
		<xsl:call-template name="asscell">
		  <xsl:with-param name="assindex" select="$assindex - 1"/>
		</xsl:call-template>
	  </table>
	</div>

	<div class='grades' id='gradesright'>
	  Year's Work
	  <table>
		<xsl:call-template name="asscell">
		  <xsl:with-param name="index" select="$assindex - 1"/>
		  <xsl:with-param name="assindex" select="$assindex"/>
		</xsl:call-template>
	  </table>
	</div>

	<div class='spacer'>
	  <xsl:text></xsl:text>
	</div>

	  <table class="categories">
		<tr>
		  <th>
		  </th>
		  <xsl:call-template name="ratheader">
			<xsl:with-param name="ratindex" select="$ratindex"/>
		  </xsl:call-template>
		</tr>

		<xsl:apply-templates select="comments/comment/categories">
		  <xsl:with-param name="ratindex" select="$ratindex"/>
		</xsl:apply-templates>
	  </table>

	<div class='subcomment'>
	  Subject Teachers' Comments<br />
	  <xsl:apply-templates select="comments"/>
	  <xsl:text>&#160;</xsl:text>
	</div>

  <hr />
</xsl:template>

<xsl:template match="comments">
  <xsl:apply-templates select="comment"/>
</xsl:template>

<xsl:template match="comment">
<p class="comment-text">
	<xsl:value-of select="text/value/text()" />
</p>
<p class="signature">
	<xsl:value-of select="teacher/value/text()" />
</p>
</xsl:template>

<xsl:template name="asscell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="assindex">1</xsl:param>
  <xsl:variable name='asslabel' select='../asstable/ass/label' />

  <xsl:if test="$index &lt; $assindex">
	<xsl:if test="assessments/assessment[printlabel/value=$asslabel[$index]]/result/value/text()!=' '">
	<tr>
	  <th>
		<label>
		  <xsl:value-of select="$asslabel[$index]" />  
		  <xsl:text>&#160; </xsl:text>
		</label>
	  </th>
	  <td>
		<xsl:value-of select="assessments/assessment[printlabel/value=$asslabel[$index]]/result/value/text()" />  
		<xsl:text>&#160; </xsl:text>
	  </td>
	</tr>
	<xsl:call-template name="asscell">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="assindex" select="$assindex"/>
    </xsl:call-template>
	</xsl:if>
  </xsl:if>
</xsl:template>

<xsl:template name="ratheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="ratindex">1</xsl:param>
  <xsl:variable name='ratname' select='../cattable/rat/name' />
  <xsl:if test="$index &lt; $ratindex">
	<th>
		<xsl:value-of select="$ratname[$index]" />  
		<xsl:text>&#160; </xsl:text>
	</th>
    <xsl:call-template name="ratheader">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="ratindex" select="$ratindex"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template match="comments/comment/categories">
  <xsl:param name="ratindex">1</xsl:param>
  <xsl:apply-templates select="category">
	<xsl:with-param name="ratindex" select="$ratindex"/>
  </xsl:apply-templates>
</xsl:template>

<xsl:template match="category">
  <xsl:param name="ratindex">1</xsl:param>
  <tr>
	<th>
	  <xsl:value-of select="label/text()" />
	</th>
	<xsl:call-template name="ratcell">
	  <xsl:with-param name="ratindex" select="$ratindex"/>
	</xsl:call-template>
  </tr>
</xsl:template>

<xsl:template name="ratcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="ratindex">1</xsl:param>
  <xsl:variable name='ratvalue' select='../../../../../cattable/rat/value' />

  <xsl:if test="$index &lt; $ratindex">
	  <td>
		<xsl:if test="value/text() + 1 = 1 + $ratvalue[$index]">
			<img src="../class/images/blacktick.png" width="12px"/>
		</xsl:if>
		<xsl:text>&#160; </xsl:text>
	  </td>
	<xsl:call-template name="ratcell">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="ratindex" select="$ratindex"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
